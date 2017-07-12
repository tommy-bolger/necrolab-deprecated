<?php
namespace Modules\Necrolab\Models\Rankings\Cache;

use \Modules\Necrolab\Models\Necrolab;

class Entry
extends Necrolab {
    public static function saveFromLeaderboardEntry(array $leaderboard_entry, $cache) {
        if(!empty($leaderboard_entry)) {
            $steam_user_id = $leaderboard_entry['steam_user_id'];
            $release_id = $leaderboard_entry['release_id'];
            $mode_id = $leaderboard_entry['mode_id'];
            $seeded = $leaderboard_entry['is_seeded'];
            
            $cache->hSetNx(CacheNames::getPowerRankingModesName($release_id, $seeded), $mode_id, $mode_id);
        
            $power_ranking_entry_hash_name = CacheNames::getPowerRankingEntryName($release_id, $mode_id, $seeded, $steam_user_id);
            
            $character_column_prefix = $leaderboard_entry['character_name'];
            $pb_id_name = $character_column_prefix;
            $rank_column_name = '';
            
            $power_ranking_entry_record = array();               
            
            if($leaderboard_entry['is_speedrun'] == 1) {
                $pb_id_name .= '_speed';
                $rank_column_name = "{$character_column_prefix}_speed_rank";
            }
            elseif($leaderboard_entry['is_deathless'] == 1) {
                $pb_id_name .= '_deathless';
                $rank_column_name = "{$character_column_prefix}_deathless_rank"; 
            }
            elseif($leaderboard_entry['is_score_run'] == 1) {
                $pb_id_name .= '_score';
                $rank_column_name = "{$character_column_prefix}_score_rank";
            }
            
            $pb_id_name .= '_pb_id';
            
            $rank = $leaderboard_entry['rank'];
            $rank_points = static::generateRankPoints($rank);
            
            $power_ranking_entry_record['steam_user_id'] = $steam_user_id;
            $power_ranking_entry_record[$pb_id_name] = $leaderboard_entry['steam_user_pb_id'];
            $power_ranking_entry_record[$rank_column_name] = $rank;

            $cache->zIncrBy(CacheNames::getCharacterPointsName($release_id, $mode_id, $seeded, $character_column_prefix), $rank_points, $steam_user_id);
            $cache->zIncrBy(CacheNames::getPowerTotalPointsName($release_id, $mode_id, $seeded), $rank_points, $steam_user_id);
            
            if($leaderboard_entry['is_deathless'] == 1) {
                $cache->zIncrBy(CacheNames::getDeathlessPointsName($release_id, $mode_id, $seeded), $rank_points, $steam_user_id);
            }
            elseif($leaderboard_entry['is_score_run'] == 1) {
                $cache->zIncrBy(CacheNames::getScorePointsName($release_id, $mode_id, $seeded), $rank_points, $steam_user_id);
            }
            elseif($leaderboard_entry['is_speedrun'] == 1) {
                $cache->zIncrBy(CacheNames::getSpeedPointsName($release_id, $mode_id, $seeded), $rank_points, $steam_user_id);
            }
            
            $cache->hMSet($power_ranking_entry_hash_name, $power_ranking_entry_record);
        }
    }
}