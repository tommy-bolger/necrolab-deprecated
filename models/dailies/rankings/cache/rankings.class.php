<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Cache;

use \Modules\Necrolab\Models\Dailies\Rankings\Rankings as BaseRankings;

class Rankings
extends BaseRankings {
    public static function generateRanksFromPoints($release_id, $mode_id, $daily_ranking_day_type_id, $cache) {      
        $total_points_entries = static::getTotalPointsByRank($release_id, $mode_id, $daily_ranking_day_type_id, $cache);
        
        if(!empty($total_points_entries)) {
            $transaction = $cache->transaction();
            
            foreach($total_points_entries as $rank => $steam_user_id) { 
                $real_rank = $rank + 1;
                
                $transaction->hSet(CacheNames::getEntryName($release_id, $mode_id, $daily_ranking_day_type_id, $steam_user_id), 'rank', $real_rank);
            }
            
            $transaction->commit();
        }
    }

    public static function getTotalPointsByRank($release_id, $mode_id, $daily_ranking_day_type_id, $cache) {      
        return $cache->zRevRange(CacheNames::getTotalPointsName($release_id, $mode_id, $daily_ranking_day_type_id), 0, -1);
    }
    
    public static function getModesUsed($release_id, $cache) {
        return $cache->hGetAll(CacheNames::getModesName($release_id));
    }
    
    public static function getNumberOfDaysModesUsed($release_id, $mode_id, $cache) {
        return $cache->hGetAll(CacheNames::getModeNumberOfDaysName($release_id, $mode_id));
    }
}