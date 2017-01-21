<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Cache;

use \DateTime;
use \PDO;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Dailies\Rankings\Rankings as BaseRankings;
use \Modules\Necrolab\Models\DailyRankings\Database\DailyRankings as DatabaseDailyRankings;
use \Modules\Necrolab\Models\DailyRankings\Cache\RecordModels\DailyRankingEntry;
use \Modules\Necrolab\Models\SteamUsers\Cache\SteamUsers;

class Rankings
extends BaseRankings {
    public static function generateRanksFromPoints($daily_ranking_day_type_id, $cache = NULL) {  
        if(empty($cache)) {
            $cache = cache();
        }
    
        $total_points_entries = static::getTotalPointsByRank($daily_ranking_day_type_id, $cache);
        
        $transaction = $cache->transaction();
        
        foreach($total_points_entries as $rank => $steam_user_id) { 
            $real_rank = $rank + 1;
            
            $transaction->hSet(CacheNames::getEntryName($steam_user_id, $daily_ranking_day_type_id), 'rank', $real_rank);
        }
        
        $transaction->commit();
    }

    public static function getTotalPointsByRank($daily_ranking_day_type_id, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache();
        }        
        
        return $cache->zRevRange(CacheNames::getTotalPointsName($daily_ranking_day_type_id), 0, -1);
    }

    public static function getRankingsResultset($number_of_days = NULL) {
        $rankings_name = CacheNames::getRankingsName($number_of_days);
        $ranking_entries_name = CacheNames::getEntriesName($number_of_days);
        $ranking_entries_filter_name = CacheNames::getEntriesFilterName($number_of_days);
        
        $resultset = new Redis($rankings_name, cache('read'));
        
        $resultset->setEntriesName($ranking_entries_name);
        $resultset->setFilterName($ranking_entries_filter_name);
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processRankingsResulset($result_data);
        });
        
        return $resultset;
    }
    
    public static function deleteRankings() {
        $cache = cache('write');
        
        $lua_script_path = static::getLuaScriptPath();
    
        $cache->reval(file_get_contents("{$lua_script_path}/delete_daily_ranking_season.lua"), array(
            CacheNames::getRankingsName(),
            CacheNames::getEntriesName(),
            CacheNames::getEntriesFilterName()
        ));
        
        $day_types = DatTypes::getDayTypes();
        
        if(!empty($day_types)) {
            foreach($day_types as $number_of_days => $daily_ranking_day_type_id) {
                $cache->reval(file_get_contents("{$lua_script_path}/delete_daily_ranking_season.lua"), array(
                    CacheNames::getRankingsName($number_of_days),
                    CacheNames::getEntriesName($number_of_days),
                    CacheNames::getEntriesFilterName($number_of_days)
                ));
            }
        }
    }
    
    public static function loadIntoCache() {
        $cache = cache('write');
        
        $query = static::getLatestRankingsQuery();
        
        $resultset = DatabaseDailyRankings::getLatestEntries();

        $latest_daily_rankings = $resultset->prepareExecuteQuery();

        $transaction = $cache->transaction();

        while($latest_daily_ranking = $latest_daily_rankings->fetch(PDO::FETCH_ASSOC)) {
            $personaname = $latest_daily_ranking['personaname'];
            $number_of_days = $latest_daily_ranking['number_of_days'];
        
            $daily_ranking_entry = new DailyRankingEntry();
            $daily_ranking_entry->setPropertiesFromArray($latest_daily_ranking);
            
            $entry_hash_name = CacheNames::getEntryName($daily_ranking_entry->steamid, $number_of_days);
            
            $transaction->hMset($entry_hash_name, $daily_ranking_entry->toArray(false));
            $transaction->zAdd(CacheNames::getEntriesName($number_of_days), $daily_ranking_entry->rank, $entry_hash_name);
            
            if(!empty($personaname)) {
                $transaction->hSet(CacheNames::getEntriesFilterName($number_of_days), $personaname, $entry_hash_name);
            }
        }

        $transaction->set(CacheNames::getLastUpdatedName(), date('Y-m-d H:i:s'));
        
        $transaction->commit();
    }
    
    public static function getSteamUsersFromResultData($result_data) {
        $steamids = array();
        
        if(!empty($result_data)) {
            foreach($result_data as $result_row) {
                $steamids[] = $result_row['steamid'];
            }
        }
        
        return SteamUsers::getSocialMediaData($steamids);
    }
}