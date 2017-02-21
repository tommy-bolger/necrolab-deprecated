<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Modules\Necrolab\Models\Leaderboards\Entry as BaseEntry;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\LeaderboardEntry as DatabaseEntry;
use \Modules\Necrolab\Models\Releases\Database\Releases as DatabaseReleases;

class Entry
extends BaseEntry {
    public static function save(DateTime $date, DatabaseEntry $database_entry) {
        $date_formatted = $date->format('Y_m');
    
        db()->insert("leaderboard_entries_{$date_formatted}", $database_entry->toArray(), "leaderboard_entries_{$date_formatted}_insert", false);
    }
    
    public static function update(DateTime $date, $leaderboard_snapshot_id, $steam_user_id, $rank, DatabaseEntry $database_entry, $query_cache_name = NULL) {
        $date_formatted = $date->format('Y_m');
        
        $leaderboard_record = array();
        
        if(!empty($query_cache_name)) {
            $leaderboard_record = $database_entry->toArray();
        }
        else {
            $leaderboard_record = $database_entry->toArray(false);
        }
        
        if(array_key_exists('leaderboard_snapshot_id', $leaderboard_record)) {
            unset($leaderboard_record['leaderboard_snapshot_id']);
        }
        
        if(array_key_exists('steam_user_id', $leaderboard_record)) {
            unset($leaderboard_record['steam_user_id']);
        }
        
        if(array_key_exists('rank', $leaderboard_record)) {
            unset($leaderboard_record['rank']);
        }
        
        if(array_key_exists('steam_replay_id', $leaderboard_record)) {
            unset($leaderboard_record['steam_replay_id']);
        }
        
        if(array_key_exists('leaderboard_entry_details_id', $leaderboard_record)) {
            unset($leaderboard_record['leaderboard_entry_details_id']);
        }
        
        db()->update("leaderboard_entries_{$date_formatted}", $leaderboard_record, array(
            'leaderboard_snapshot_id' => $leaderboard_snapshot_id,
            'steam_user_id' => $steam_user_id,
            'rank' => $rank
        ), '', $query_cache_name);
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'le.steam_user_id',
                'alias' => 'steam_user_id',
            ),
            array(
                'field' => 'le.score',
                'alias' => 'score',
            ),
            array(
                'field' => 'le.time',
                'alias' => 'time',
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank',
            ),
            array(
                'field' => 'le.is_win',
                'alias' => 'is_win',
            ),
            array(
                'field' => 'le.zone',
                'alias' => 'zone',
            ),
            array(
                'field' => 'le.level',
                'alias' => 'level',
            ),
            array(
                'field' => 'le.win_count',
                'alias' => 'win_count',
            )
        ));
    }
    
    public static function getIfWin(DateTime $date, $release_id, $zone, $level) {    
        $is_win = 0;
        
        $release = DatabaseReleases::getByDateAndId($date, $release_id);
        
        if(!empty($release)) {                    
            if($zone == $release['win_zone'] && $level == $release['win_level']) {
                $is_win = 1;
            }
        }
        
        return $is_win;
    }
}