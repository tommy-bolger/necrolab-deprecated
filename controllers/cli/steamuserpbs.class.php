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
    //TODO: Need to redo this function to work with the current steam_user_pbs table.
    public function actionFixEntryRecords($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {            
            db()->beginTransaction();
        
            $leaderboard_entries_resulset = DatabaseSteamUserPbs::getAllResultset($current_date);
            
            $leaderboard_entries_resulset->setAsCursor(200000);
        
            $leaderboard_entries_resulset->prepareExecuteQuery();
            
            $leaderboard_entries = array();
            
            do {
                $leaderboard_entries = $leaderboard_entries_resulset->getNextCursorChunk();
            
                if(!empty($leaderboard_entries)) {
                    foreach($leaderboard_entries as $leaderboard_entry) {
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
                }
            }
            while(!empty($leaderboard_entries));
            
            db()->commit();
            
            $current_date->add(new DateInterval('P1D'));
        }  
    }
}