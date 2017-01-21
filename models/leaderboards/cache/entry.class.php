<?php
namespace Modules\Necrolab\Models\Leaderboards\Cache;

use \Modules\Necrolab\Models\Leaderboards\Entry as BaseEntry;

class Entry
extends BaseEntry {
    protected static $imported_entry_attributes = array();
    
    public static function save(array $entry_data, $leaderboard_record, $prefix_name = '', $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        if(!empty($prefix_name)) {
            $prefix_name .= ':';
        }
        
        $lbid = $leaderboard_record->lbid;
    
        $leaderboard_entry = new LeaderboardEntry();
        $leaderboard_entry->setPropertiesFromArray($entry_data);
        
        $rank = $leaderboard_entry->rank;
        
        $leaderboard_hash_name = $prefix_name . CacheNames::getLeaderboardName($lbid);
        
        $entry_hash_name = $prefix_name . CacheNames::getEntryName($lbid, $rank);
        
        $cache->del($entry_hash_name);
        
        $record_fields = $leaderboard_entry->toArray(false);
        
        //Combine multiple rPush calls into one to reduce overhead.
        array_unshift($record_fields, $entry_hash_name);
        
        $cache->__call('rPush', $record_fields);
        
        if(!isset(static::$imported_entry_attributes[$lbid])) {            
            static::$imported_entry_attributes[$lbid] = $lbid;
        }
        
        $steamid = $leaderboard_entry->steamid;
        
        if($leaderboard_record->is_daily == 0) {
            if($leaderboard_record->is_speedrun == 1) {
                $user_leaderboard_name = SteamUserCacheNames::getSpeedrunLeaderboardsName($steamid);
                
                $cache->hSet($user_leaderboard_name, $lbid, $rank);
            }
            
            if($leaderboard_record->is_score_run == 1 && $leaderboard_record->is_deathless == 0) {
                $user_leaderboard_name = SteamUserCacheNames::getScoreLeaderboardsName($steamid);
                
                $cache->hSet($user_leaderboard_name, $lbid, $rank);
            }
            
            if($leaderboard_record->is_custom == 1) {
                $user_leaderboard_name = SteamUserCacheNames::getCustomLeaderboardsName($steamid);
                
                $cache->hSet($user_leaderboard_name, $lbid, $rank);
            }
            
            if($leaderboard_record->is_co_op == 1) {                        
                $user_leaderboard_name = SteamUserCacheNames::getCoOpLeaderboardsName($steamid);
                
                $cache->hSet($user_leaderboard_name, $lbid, $rank);
            }
            
            if($leaderboard_record->is_seeded == 1) {       
                $user_leaderboard_name = SteamUserCacheNames::getSeededLeaderboardsName($steamid);
                
                $cache->hSet($user_leaderboard_name, $lbid, $rank);
            }
            
            if($leaderboard_record->is_deathless == 1) {      
                $user_leaderboard_name = SteamUserCacheNames::getDeathlessLeaderboardsName($steamid);
                
                $cache->hSet($user_leaderboard_name, $lbid, $rank);
            }
            
            if($leaderboard_record->is_power_ranking == 1) {
                $user_leaderboard_name = SteamUserCacheNames::getPowerLeaderboardsName($steamid);
                
                $cache->hSet($user_leaderboard_name, $lbid, $rank);
            }
        
            $user_leaderboard_name = SteamUserCacheNames::getCharacterLeaderboardsName($steamid, $leaderboard_record->character_name);
            
            $cache->hSet($user_leaderboard_name, $lbid, $rank);
        }
        
        return $entry_hash_name;
    }
}