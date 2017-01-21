<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Speed;

use \DateTime;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Rankings\Cache\Rankings as BaseRankings;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames;

class Rankings
extends BaseRankings {
    public static function generateRanksFromPoints(DateTime $date, $cache = NULL) {  
        if(empty($cache)) {
            $cache = cache();
        }  
    
        $total_points_entries = static::getTotalPointsByRank($date);
        
        $transaction = $cache->transaction();
        
        foreach($total_points_entries as $rank => $steam_user_id) {        
            $real_rank = $rank + 1;
            
            $transaction->hSet(CacheNames::getPowerRankingEntryName($steam_user_id), 'speed_rank', $real_rank);
        }
        
        $transaction->commit();
    }

    public static function getTotalPointsByRank(DateTime $date, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache();
        }        
        
        return $cache->zRevRange(CacheNames::getSpeedPointsName(), 0, -1);
    }

    public static function getLatestRankingsResultset() {
        $cache = cache('read');
    
        $resultset = new Redis(CacheNames::getSpeedRankingName(), $cache);
        
        $resultset->setEntriesName(CacheNames::getSpeedEntriesName());
        $resultset->setFilterName(CacheNames::getSpeedEntriesFilterName());   
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processCategoryResultset('speed', __NAMESPACE__ . '\RecordModels\SpeedEntry', $result_data);
        });
        
        return $resultset;
    }
}