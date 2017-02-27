<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Character;

use \DateTime;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Rankings\Cache\Rankings as BaseRankings;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames;

class Rankings
extends BaseRankings {
    public static function generateRanksFromPoints(DateTime $date, $character_name, $cache) {  
        $total_points_entries = static::getTotalPointsByRank($date, $character_name, $cache);
        
        $transaction = $cache->transaction();
        
        foreach($total_points_entries as $rank => $steam_user_id) {
            $real_rank = $rank + 1;
            
            $transaction->hSet(CacheNames::getPowerRankingEntryName($steam_user_id), "{$character_name}_rank", $real_rank);
        }
        
        $transaction->commit();
    }

    public static function getTotalPointsByRank(DateTime $date, $character_name, $cache) {
        return $cache->zRevRange(CacheNames::getCharacterPointsName($character_name), 0, -1);
    }
}