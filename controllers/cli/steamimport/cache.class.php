<?php
namespace Modules\Necrolab\Controllers\Cli\SteamImport;

use \DateTime;

use \Modules\Necrolab\Models\Leaderboards\Cache\Blacklist;
use \Modules\Necrolab\Models\Leaderboards\Cache\Leaderboards;
use \Modules\Necrolab\Models\Dailies\Seasons\Cache\Seasons;
use \Modules\Necrolab\Models\Dailies\Seasons\Leaderboards\Cache\Leaderboards as SeasonLeaderboards;
use \Modules\Necrolab\Models\Dailies\Leaderboards\Cache\Leaderboards as DailyLeaderboards;
use \Modules\Necrolab\Models\Dailies\Leaderboards\Cache\Entries as DailyLeaderboardEntries;

class Cache
extends BaseProcess {
    protected function getEntriesTransaction() {    
        return cache('write', true)->transaction();
    }
    
    protected function commitEntriesTransaction($transaction) {
        $transaction->commit();
    }
    
    protected function importEntriesChildPostProcess($transaction, $leaderboard_record, $max_rank) {
        if($leaderboard_record->is_daily != 1) {
            static::saveEntries($leaderboard_record->lbid, $max_rank, '', $transaction);
        }
        else {                   
            if(!empty($leaderboard_record->is_daily_ranking == 1)) {
                DailyLeaderboardEntries::save($leaderboard_record->lbid, $max_rank, $transaction);
            }
        }
    }

    protected function importPageEntries($leaderboard_record, $leaderboard_users, &$max_rank, &$transaction) {
        $entries = array();

        if(!empty($leaderboard_users->entries->entry)) {
            if(is_array($leaderboard_users->entries->entry)) {
                $entries = $leaderboard_users->entries->entry;
            }
            else {
                $entries[] = $leaderboard_users->entries->entry;
            }
        }

        if(!empty($entries)) {                                    
            foreach($entries as &$entry) {
                $leaderboard_entry = new SteamLeaderboardEntry();
                $leaderboard_entry->setPropertiesFromSteamObject($entry, $leaderboard_record);
            
                $score = $entry->score;
                
                //Any scores from a score run greater than 300000 gold would be considered cheating and should be excluded from the rankings.
                if(($leaderboard_record->is_score_run == 1 && $leaderboard_entry->score <= 300000) || $leaderboard_record->is_speedrun == 1) {  
                    $max_rank += 1;
                    
                    if(!SteamUsers::userIdExists($leaderboard_entry->steamid)) {
                        SteamUsers::saveUser(array(
                            'steamid' => $leaderboard_entry->steamid,
                            'updated' => strtotime('-30 days')
                        ), $transaction);
                        
                        SteamUsers::addUserId($leaderboard_entry->steamid);
                    }
                    
                    if($leaderboard_record->is_daily != 1) {                    
                        static::saveEntry($leaderboard_entry->toArray(), $leaderboard_record, '', $transaction);
                    }
                    else {                   
                        if(!empty($leaderboard_record->is_daily_ranking == 1)) {
                            DailyRankings::saveDailyLeaderboardEntry($leaderboard_entry->toArray(), $leaderboard_record, $transaction);
                        }
                    }
                }
            }
        }
    }
    
    public function init() {
        Blacklist::load();
    }
    
    protected function getBlacklistRecord($lbid) {
        return Blacklist::getRecordById($lbid);
    }
    
    protected function saveImportedLeaderboards() {
        if(!empty($this->imported_leaderboards)) { 
            $active_season = Seasons::getActive($this->as_of_date);

            $transaction = cache('write')->transaction();

            foreach($this->imported_leaderboards as $imported_leaderboard) {
                if($imported_leaderboard->is_daily != 1) {
                    Leaderboards::save($imported_leaderboard->toArray(), $imported_leaderboard->lbid, '', $transaction);
                }
                else {                   
                    if(!empty($imported_leaderboard->is_daily_ranking == 1)) {
                        DailyLeaderboards::save($imported_leaderboard->toArray(), $this->as_of_date, $transaction);
                    
                        if(!empty($active_season)) {
                            if(Seasons::dateInRange($imported_leaderboard->daily_date_object, new DateTime($active_season['start_date']), new DateTime($active_season['end_date']))) {
                                SeasonLeaderboards::save($imported_leaderboard->toArray(), $transaction);
                            }
                        }
                    }
                }
            }
            
            $transaction->commit();
        }
    }
    
    protected function importLeaderboardEntries() {
        if(!empty(self::$imported_leaderboards)) {
            SteamUsers::loadUserIds();
            
            parent::importLeaderboardEntries();
        }
    }
}