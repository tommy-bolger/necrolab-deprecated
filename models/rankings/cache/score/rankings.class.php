<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Score;

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
            
            $transaction->hSet(CacheNames::getPowerRankingEntryName($steam_user_id), 'score_rank', $real_rank);
        }
        
        $transaction->commit();
    }

    public static function getTotalPointsByRank(DateTime $date, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache();
        }        
        
        return $cache->zRevRange(CacheNames::getScorePointsName(), 0, -1);
    }

    public static function getLatestRankingsResultset() {
        $cache = cache('read');
    
        $resultset = new Redis(CacheNames::getScoreRankingName(), $cache);
        
        $resultset->setEntriesName(CacheNames::getScoreEntriesName());
        $resultset->setFilterName(CacheNames::getScoreEntriesFilterName());  
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processCategoryResultset('score', __NAMESPACE__ . '\RecordModels\ScoreEntry', $result_data);
        });
        
        return $resultset;
    }
}