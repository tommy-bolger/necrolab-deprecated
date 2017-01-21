<?php
namespace Modules\Necrolab\Models\Leaderboards\Cache;

use \Modules\Necrolab\Models\Leaderboards\Leaderboards as BaseLeaderboards;
use \Modules\Necrolab\Models\Leaderboards\Cache\RecordModels\Leaderboard;

class Leaderboards
extends BaseLeaderboards {
    public static function loadLeaderboard($lbid) {
        if(empty(static::$leaderboards[$lbid])) {
            $leaderboard_entry_name = CacheNames::getLeaderboardName($lbid);
            
            $leaderboard_record = new Leaderboard();
            
            $leaderboard_record->setPropertiesFromIndexedArray(cache('read')->lRange($leaderboard_entry_name, 0, -1));
        
            static::$leaderboards[$lbid] = $leaderboard_record;
        }
    }

    public static function getAllByCategory($category_name) {
        $cache = cache('read');
    
        $leaderboard_category_hash_name = '';
        
        switch($category_name) {
            case 'score':
                $leaderboard_category_hash_name = CacheNames::getScoreLeaderboardsName();
                break;
            case 'speed':
                $leaderboard_category_hash_name = CacheNames::getSpeedrunLeaderboardsName();
                break;
            case 'deathless':
                $leaderboard_category_hash_name = CacheNames::getDeathlessLeaderboardsName();
                break;
        }
        
        $leaderboard_names = $cache->sMembers($leaderboard_category_hash_name);
        
        $leaderboards_in_category = array();
        
        if(!empty($leaderboard_names)) {            
            $leaderboards_in_category = $cache->lRangeMulti($leaderboard_names);
        }
        
        $leaderboard_records = array();
        
        if(!empty($leaderboards_in_category)) {
            foreach($leaderboards_in_category as $leaderboard_in_category) {
                $leaderboard_record = new Leaderboard();
                
                $leaderboard_record->setPropertiesFromIndexedArray($leaderboard_in_category);
                
                $leaderboard_records[] = $leaderboard_record;
            }
        }
        
        return $leaderboard_records;
    }
    
    public static function save(array $leaderboard_data, $lbid, $prefix_name = '', $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        if(!empty($prefix_name)) {
            $prefix_name .= ':';
        }
        
        $leaderboard = new Leaderboard();
        $leaderboard->setPropertiesFromArray($leaderboard_data);
    
        $leaderboard_name = $prefix_name . CacheNames::getLeaderboardName($lbid);
        
        $cache->del($leaderboard_name);
        
        $leaderboard_record_fields = $leaderboard->toArray(false);
        $leaderboard_record_fields = array_values($leaderboard_record_fields);
        
        array_unshift($leaderboard_record_fields, $leaderboard_name);
        
        $cache->__call('rPush', $leaderboard_record_fields);
        
        $cache->sAdd(CacheNames::getLeaderboardsName(), $leaderboard_name);
        
        if($leaderboard->is_daily == 0) {
            if($leaderboard->is_speedrun == 1) {
                $cache->sAdd(CacheNames::getSpeedrunLeaderboardsName(), $leaderboard_name);
            }
            
            if($leaderboard->is_score_run == 1 && $leaderboard->is_deathless == 0) {
                $cache->sAdd(CacheNames::getScoreLeaderboardsName(), $leaderboard_name);
            }
            
            if($leaderboard->is_custom == 1) {
                $cache->sAdd(CacheNames::getCustomLeaderboardsName(), $leaderboard_name);
            }
            
            if($leaderboard->is_co_op == 1) {                        
                $cache->sAdd(CacheNames::getCoOpLeaderboardsName(), $leaderboard_name);
            }
            
            if($leaderboard->is_seeded == 1) {       
                $cache->sAdd(CacheNames::getSeededLeaderboardsName(), $leaderboard_name);
            }
            
            if($leaderboard->is_deathless == 1) {      
                $cache->sAdd(CacheNames::getDeathlessLeaderboardsName(), $leaderboard_name);
            }
            
            if($leaderboard->is_power_ranking == 1) {
                $cache->sAdd(CacheNames::getPowerLeaderboardsName(), $leaderboard_name);
            }
            
            $cache->sAdd(CacheNames::getCharacterLeaderboardsName($leaderboard->character_name), $leaderboard_name);
        }
    }
}