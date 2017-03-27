<?php
namespace Modules\Necrolab\Controllers\Cli;

use \DateTime;
use \DateInterval;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Leaderboards\Database\Entries as DatabaseLeaderboardEntries;
use \Modules\Necrolab\Models\Leaderboards\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\SteamUsers\Database\Pbs as DatabaseSteamUserPbs;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUserPb as DatabaseSteamUserPb;

class SteamUserPbs
extends Cli { 
    protected function populateFromEntries(DateTime $date) {    
        $database = db();

        $database->beginTransaction();
    
        $leaderboard_entries_resultset = DatabaseLeaderboardEntries::getSteamPbPopulateResultset($date);
        
        $leaderboard_entries_resultset->prepareExecuteQuery();
        
        $leaderboard_entries = array();
        
         do {
            $leaderboard_entries = $database->getAll("
                FETCH 100000
                FROM archived_leaderboard_data_{$date->format('Y_m')}
            ");
         
            if(!empty($leaderboard_entries)) {
                foreach($leaderboard_entries as $leaderboard_entry) {
                    $leaderboard_id = (int)$leaderboard_entry['leaderboard_id'];
                    $leaderboard_snapshot_id = (int)$leaderboard_entry['leaderboard_snapshot_id'];
                    $steam_user_id = (int)$leaderboard_entry['steam_user_id'];
                    $score = (int)$leaderboard_entry['score'];
                    $rank = (int)$leaderboard_entry['rank'];
                
                    $steam_user_pb_id = DatabaseSteamUserPbs::getId($leaderboard_id, $steam_user_id, $score);
                    
                    if(empty($steam_user_pb_id)) {
                        $steam_user_pb_record = new DatabaseSteamUserPb();

                        $steam_user_pb_record->leaderboard_id = $leaderboard_id;
                        $steam_user_pb_record->steam_user_id = $steam_user_id;
                        $steam_user_pb_record->score = $score;
                        $steam_user_pb_record->first_leaderboard_snapshot_id = $leaderboard_snapshot_id;
                        $steam_user_pb_record->first_rank = $rank;
                        $steam_user_pb_record->time = $leaderboard_entry['time'];
                        $steam_user_pb_record->win_count = $leaderboard_entry['win_count'];
                        $steam_user_pb_record->zone = $leaderboard_entry['zone'];
                        $steam_user_pb_record->level = $leaderboard_entry['level'];
                        $steam_user_pb_record->is_win = $leaderboard_entry['is_win'];
                        $steam_user_pb_record->leaderboard_entry_details_id = $leaderboard_entry['leaderboard_entry_details_id'];
                        $steam_user_pb_record->steam_replay_id = $leaderboard_entry['steam_replay_id'];
                    
                        $steam_user_pb_id = DatabaseSteamUserPbs::save($steam_user_pb_record, 'populate');
                    }
                    
                    DatabaseEntry::save($date, array(
                        'leaderboard_snapshot_id' => $leaderboard_snapshot_id,
                        'steam_user_pb_id' => $steam_user_pb_id,
                        'rank' => $rank
                    ), "populate_entries_{$date->format('Y-m-01')}");
                }
            }
        }
        while(!empty($leaderboard_entries));
        
        DatabaseLeaderboardEntries::closeSteamPbPopulateResultset($date);
        
        $database->commit();
    }
    
    public function actionPopulateFromEntries($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) { 
            $this->populateFromEntries($current_date);
        
            $current_date->add(new DateInterval('P1M'));
        }  
    }

    public function actionFixEntryRecords($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {            
            db()->beginTransaction();
        
            $leaderboard_entries_resulset = DatabaseLeaderboardEntries::getAllBaseResultset($current_date);
        
            $leaderboard_entries = $leaderboard_entries_resulset->prepareExecuteQuery();
            
            while($leaderboard_entry = db()->getStatementRow($leaderboard_entries)) {
                $entry_record = new DatabaseLeaderboardEntry();
                
                $score = $leaderboard_entry['score'];
                
                $entry_record->score = $score;
                
                if(!empty($leaderboard_entry['is_speedrun'])) {
                    $entry_record->time = Entry::getTime($score);
                    $entry_record->is_win = 1;
                }
                else {
                    $zone_level = Entry::getHighestZoneLevel($leaderboard_entry['details']);
                
                    $entry_record->zone = $zone_level['highest_zone'];
                    $entry_record->level = $zone_level['highest_level'];
                    
                    $entry_record->is_win = Entry::getIfWin($current_date, $leaderboard_entry['release_id'], $entry_record->zone, $entry_record->level);
                
                    if(!empty($leaderboard_entry['is_deathless'])) {
                        $entry_record->win_count = Entry::getWinCount($score);
                    }
                }
                
                Entry::update(
                    $current_date, 
                    $leaderboard_entry['leaderboard_snapshot_id'], 
                    $leaderboard_entry['steam_user_id'], 
                    $leaderboard_entry['rank'], 
                    $entry_record,
                    "fix_record_update_{$current_date->format('Y_m')}"
                );
            }
            
            db()->commit();
            
            $current_date->add(new DateInterval('P1D'));
        }  
    }
}