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
    public static function generateRanksFromPoints($daily_ranking_day_type_id, $cache) {      
        $total_points_entries = static::getTotalPointsByRank($daily_ranking_day_type_id, $cache);
        
        $transaction = $cache->transaction();
        
        foreach($total_points_entries as $rank => $steam_user_id) { 
            $real_rank = $rank + 1;
            
            $transaction->hSet(CacheNames::getEntryName($steam_user_id, $daily_ranking_day_type_id), 'rank', $real_rank);
        }
        
        $transaction->commit();
    }

    public static function getTotalPointsByRank($daily_ranking_day_type_id, $cache) {      
        return $cache->zRevRange(CacheNames::getTotalPointsName($daily_ranking_day_type_id), 0, -1);
    }
}