<?php
namespace Modules\Necrolab\Models\Rankings\Cache;

use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
use \Modules\Necrolab\Models\Necrolab;

class Entry
extends Necrolab {
    public static function saveFromLeaderboardEntry(array $leaderboard_entry, $leaderboard_record, $cache) {
        if(!empty($leaderboard_entry)) {
            $steam_user_id = $leaderboard_entry['steam_user_id'];
        
            $power_ranking_entry_hash_name = CacheNames::getPowerRankingEntryName($steam_user_id);
            
            $character = DatabaseCharacters::getById($leaderboard_entry['character_id']);
            
            $character_column_prefix = $character['name'];
            $entry_score = 0;
            $total_column_name = '';
            $total_points_column_name = '';
            $rank_column_name = '';
            $rank_points_column_name = '';
            
            $power_ranking_entry_record = array();               
            
            if($leaderboard_record['is_speedrun'] == 1) {
                $rank_column_name = "{$character_column_prefix}_speed_rank";
                $rank_points_column_name = "{$character_column_prefix}_speed_rank_points";
                $total_column_name = 'speed_total_time';
                $total_points_column_name = 'speed_rank_points_total';
                
                $entry_score = $leaderboard_entry['time'];
                
                $power_ranking_entry_record["{$character_column_prefix}_speed_time"] = $entry_score;
            }
            elseif($leaderboard_record['is_deathless'] == 1) {
                $rank_column_name = "{$character_column_prefix}_deathless_rank"; 
                $rank_points_column_name = "{$character_column_prefix}_deathless_rank_points";
                $total_column_name = 'deathless_total_wins';
                $total_points_column_name = 'deathless_rank_points_total';
                
                $entry_score = $leaderboard_entry['win_count'];
                
                $power_ranking_entry_record["{$character_column_prefix}_deathless_win_count"] = $entry_score;
            }
            elseif($leaderboard_record['is_score_run'] == 1) {
                $rank_column_name = "{$character_column_prefix}_score_rank";
                $rank_points_column_name = "{$character_column_prefix}_score_rank_points";
                $total_column_name = 'score_total';
                $total_points_column_name = 'score_rank_points_total';
                
                $entry_score = $leaderboard_entry['score'];
                
                $power_ranking_entry_record["{$character_column_prefix}_score"] = $entry_score;
            }
            
            $rank = $leaderboard_entry['rank'];
            $rank_points = static::generateRankPoints($rank);
            
            $power_ranking_entry_record['steam_user_id'] = $leaderboard_entry['steam_user_id'];
            $power_ranking_entry_record[$rank_column_name] = $rank;
            $power_ranking_entry_record[$rank_points_column_name] = $rank_points;
            
            $cache->hIncrByFloat($power_ranking_entry_hash_name, $total_column_name, $entry_score);
            $cache->hIncrByFloat($power_ranking_entry_hash_name, $total_points_column_name, $rank_points);
            $cache->hIncrByFloat($power_ranking_entry_hash_name, "{$character_column_prefix}_rank_points", $rank_points);
            $cache->hIncrByFloat($power_ranking_entry_hash_name, 'total_points', $rank_points);

            $cache->zIncrBy(CacheNames::getCharacterPointsName($character_column_prefix), $rank_points, $steam_user_id);
            $cache->zIncrBy(CacheNames::getPowerTotalPointsName(), $rank_points, $steam_user_id);
            
            if($leaderboard_record['is_deathless'] == 1) {
                $cache->zIncrBy(CacheNames::getDeathlessPointsName(), $rank_points, $steam_user_id);
            }
            elseif($leaderboard_record['is_score_run'] == 1) {
                $cache->zIncrBy(CacheNames::getScorePointsName(), $rank_points, $steam_user_id);
            }
            elseif($leaderboard_record['is_speedrun'] == 1) {
                $cache->zIncrBy(CacheNames::getSpeedPointsName(), $rank_points, $steam_user_id);
            }
            
            $cache->hMSet($power_ranking_entry_hash_name, $power_ranking_entry_record);
        }
    }
}