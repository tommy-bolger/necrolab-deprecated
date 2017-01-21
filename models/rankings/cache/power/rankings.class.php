<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Power;

use \DateTime;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Rankings\Cache\Rankings as BaseRankings;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames as RankingCacheNames;

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
            
            $transaction->hSet(RankingCacheNames::getPowerRankingEntryName($steam_user_id), 'rank', $real_rank);
        }
        
        $transaction->commit();
    }

    public static function getTotalPointsByRank(DateTime $date, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache();
        }        
        
        return $cache->zRevRange(RankingCacheNames::getPowerTotalPointsName(), 0, -1);
    }

    public static function getLatestRankingsResultset() {
        $cache = cache('read');
    
        $resultset = new Redis(RankingCacheNames::getPowerRankingName(), $cache);
        
        $resultset->setEntriesName(RankingCacheNames::getPowerEntriesName());
        $resultset->setFilterName(RankingCacheNames::getPowerEntriesFilterName());  
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processPowerResultset(__NAMESPACE__ . '\RecordModels\PowerRankingEntry', $result_data);
        });
        
        return $resultset;
    }
}