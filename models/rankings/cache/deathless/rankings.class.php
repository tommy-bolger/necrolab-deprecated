<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Deathless;

use \DateTime;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Rankings\Cache\Rankings as BaseRankings;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames;

class Rankings
extends BaseRankings {
    public static function generateRanksFromPoints(DateTime $date, $release_id, $mode_id, $seeded, $cache) {    
        $total_points_entries = static::getTotalPointsByRank($date, $release_id, $mode_id, $seeded, $cache);
        
        $transaction = $cache->transaction();
        
        foreach($total_points_entries as $rank => $steam_user_id) {  
            $real_rank = $rank + 1;
        
            $transaction->hSet(CacheNames::getPowerRankingEntryName($release_id, $mode_id, $seeded, $steam_user_id), 'deathless_rank', $real_rank);
        }
        
        $transaction->commit();
    }

    public static function getTotalPointsByRank(DateTime $date, $release_id, $mode_id, $seeded, $cache) {     
        return $cache->zRevRange(CacheNames::getDeathlessPointsName($release_id, $mode_id, $seeded), 0, -1);
    }
}