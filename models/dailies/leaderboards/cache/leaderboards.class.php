<?php
namespace Modules\Necrolab\Models\Dailies\Leaderboards\Cache;

use \DateTime;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\DailyRankings\Database\DailyRankings as DatabaseDailyRankings;
use \Modules\Necrolab\Models\DailyRankings\Cache\RecordModels\DailyRankingEntry;
use \Modules\Necrolab\Models\Leaderboards\Cache\Leaderboards;

class Leaderboards
extends Necrolab {         
    public static function save(array $daily_leaderboard, DateTime $as_of_date, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $lbid = $daily_leaderboard['lbid'];
        $daily_date = $as_of_date->format('Y-m-d');
    
        Leaderboards::save($daily_leaderboard, $lbid, CacheNames::getRankingsBaseName(), $cache);
            
        $leaderboard_name = CacheNames::getLeaderboardName($lbid);
        
        $cache->hSet(CacheNames::getLeaderboardsByDateName(), $daily_date, $leaderboard_name);
        
        $cache->hSet(CacheNames::getRankingsName(), $daily_date, $leaderboard_name);
    }
    
    public static function loadIntoCache($cache = NULL) {    
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $current_date = new DateTime();
        
        $day_types = DatabaseDailyRankings::getDayTypes();
        
        $resultset = DatabaseDailyRankings::getLeaderboards();
        
        $daily_leaderboards = $resultset->prepareExecuteQuery();

        $transaction = $cache->transaction();

        while($daily_leaderboard = $daily_leaderboards->fetch(PDO::FETCH_ASSOC)) {           
            static::saveDailyLeaderboard($daily_leaderboard, $day_types, $current_date, $transaction);
        }
        
        $transaction->commit();
    }
    
    public static function removeExpired(DateTime $as_of_date) {
        static::loadActiveDayTypes();
    
        if(!empty(static::$active_day_types)) {
            $ranking_day_types_leaderboards = array();
            
            foreach(static::$active_day_types as $number_of_days => $daily_ranking_day_type) { 
                $start_date = new DateTime();
                $start_date->setTimestamp(strtotime("-{$number_of_days} days"));
            
                $ranking_day_types_leaderboards[$number_of_days] = array(
                    'start_date' => $start_date,
                    'hash_name' => CacheNames::getLeaderboardsName($number_of_days),
                    'leaderboards' => cache('read')->hGetAll(CacheNames::getRankingsName($number_of_days))
                );
            }
        
            $transaction = cache('write')->transaction();
            
            foreach($ranking_day_types_leaderboards as $number_of_days => $ranking_day_type_leaderboard) {            
                $day_type_start_date = $ranking_day_type_leaderboard['start_date'];
                
                if(!empty($ranking_day_type_leaderboard['leaderboards'])) {
                    $leaderboards_by_date = $ranking_day_type_leaderboard['leaderboards'];
                    
                    foreach($leaderboards_by_date as $leaderboard_daily_date => $leaderboard_hash_name) {
                        $leaderboard_date = new DateTime($leaderboard_daily_date);
                    
                        if(!($leaderboard_date >= $day_type_start_date && $leaderboard_date <= $as_of_date)) {
                            $transaction->hDel($ranking_day_type_leaderboard['hash_name'], $leaderboard_daily_date);
                        }
                    }
                }
            }
            
            $transaction->commit();
        }
    }
    
    public static function saveDailyLeaderboardEntries($lbid, $max_rank, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        Leaderboards::saveEntries($lbid, $max_rank, CacheNames::getRankingsBaseName(), $cache);
    }
    
    public static function saveDailyLeaderboardEntry(array $leaderboard_entry, $leaderboard_record, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        Leaderboards::saveEntry($leaderboard_entry, $leaderboard_record, CacheNames::getRankingsBaseName(), $cache);
        
        $cache->hSet(SteamUserCacheNames::getDailyLeaderboardsName($leaderboard_entry['steamid']), $leaderboard_record->lbid, $leaderboard_entry['rank']);
        $cache->hSet(SteamUserCacheNames::getDailyByDateLeaderboardsName($leaderboard_entry['steamid']), $leaderboard_record->daily_date, $leaderboard_entry['rank']);
    }
    
    public static function loadDailyLeaderboardEntriesIntoCache($cache = NULL) {    
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $leaderboards_resultset = DatabaseDailyRankings::getLeaderboards();
        
        $leaderboards = $leaderboards_resultset->getAssoc();
        
        $transaction = $cache->transaction();

        $resultset = DatabaseDailyRankings::getLeaderboardEntries();
        
        $max_ranks = array();
        $daily_entries = $resultset->prepareExecuteQuery();

        while($daily_entry = $daily_entries->fetch(PDO::FETCH_ASSOC)) {     
            $lbid = $daily_entry['lbid'];
            
            $leaderboard_record = new Leaderboard();
            $leaderboard_record->setPropertiesFromArray($leaderboards[$lbid][0]);
            $leaderboard_record->lbid = $lbid;
        
            static::saveDailyLeaderboardEntry($daily_entry, $leaderboard_record, $transaction);
            
            $max_ranks[$lbid] = $daily_entry['rank'];
        }
        
        if(!empty($max_ranks)) {
            foreach($max_ranks as $lbid => $max_rank) {
                static::saveDailyLeaderboardEntries($lbid, $max_rank, $transaction);
            }
        }

        $transaction->commit();
    }
}