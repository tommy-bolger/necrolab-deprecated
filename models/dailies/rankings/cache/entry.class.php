<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Cache;

use \Modules\Necrolab\Models\Necrolab;

class Entry
extends Necrolab {
    public static function saveFromLeaderboardEntry(array $leaderboard_entry, $day_type_id, $cache = NULL) {    
        if(empty($cache)) {
            $cache = cache();
        }
        
        $steam_user_id = $leaderboard_entry['steam_user_id'];
        
        $daily_ranking_entry_hash_name = CacheNames::getEntryName($steam_user_id, $day_type_id);
        
        $cache->hSetNx($daily_ranking_entry_hash_name, 'steam_user_id', $steam_user_id);
        
        $rank = $leaderboard_entry['rank'];
        
        if($rank == 1) {
            $cache->hIncrBy($daily_ranking_entry_hash_name, 'first_place_ranks', 1);
        }
        elseif($rank <= 5) {                    
            $cache->hIncrBy($daily_ranking_entry_hash_name, 'top_5_ranks', 1);
        }
        elseif($rank <= 10) {                    
            $cache->hIncrBy($daily_ranking_entry_hash_name, 'top_10_ranks', 1);
        }
        elseif($rank <= 20) {                    
            $cache->hIncrBy($daily_ranking_entry_hash_name, 'top_20_ranks', 1);
        }
        elseif($rank <= 50) {                    
            $cache->hIncrBy($daily_ranking_entry_hash_name, 'top_50_ranks', 1);
        }
        elseif($rank <= 100) {                    
            $cache->hIncrBy($daily_ranking_entry_hash_name, 'top_100_ranks', 1);
        }
        
        $rank_points = static::generateRankPoints($rank);
        
        $cache->hIncrByFloat($daily_ranking_entry_hash_name, 'total_points', $rank_points);
        $cache->zIncrBy(CacheNames::getTotalPointsName($day_type_id), $rank_points, $steam_user_id);
        
        if($leaderboard_entry['is_win'] == 1) {
            $cache->hIncrBy($daily_ranking_entry_hash_name, 'total_wins', 1);
        }
        
        $cache->hIncrBy($daily_ranking_entry_hash_name, 'sum_of_ranks', $rank);
        $cache->hIncrBy(CacheNames::getRankSumName($day_type_id), $steam_user_id, $rank);
        
        $cache->hIncrBy($daily_ranking_entry_hash_name, 'total_dailies', 1);
        $cache->hIncrBy(CacheNames::getTotalDailiesName($day_type_id), $steam_user_id, 1);
    }
}