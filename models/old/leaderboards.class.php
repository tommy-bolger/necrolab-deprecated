<?php
namespace Modules\Necrolab\Models;

use \Exception;
use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;
use \Modules\Necrolab\Objects\CacheEntryNames;
use \Modules\Necrolab\Models\Rankings;

class Leaderboards {
    public static function getGroupedLeaderboards($category_name, array $ungrouped_leaderboards) {
        $grouped_score_leaderboards = array(
            'main' => array(
                'name' => 'All Zones Mode',
                'characters' => array()
            ),
            'seeded' => array(
                'name' => 'Seeded',
                'characters' => array()
            ),
            'custom' => array(
                'name' => 'Custom Music',
                'characters' => array()
            ),
            'seeded_custom' => array(
                'name' => 'Seeded Custom Music',
                'characters' => array()
            ),
            'co_op' => array(
                'name' => 'Co-op',
                'characters' => array()
            ),
            'seeded_co_op' => array(
                'name' => 'Seeded Co-op',
                'characters' => array()
            ),
            'co_op_custom' => array(
                'name' => 'Co-op Custom Music',
                'characters' => array()
            ),
            'seeded_co_op_custom' => array(
                'name' => 'Seeded Co-op Custom Music',
                'characters' => array()
            )
        );
        
        if(!empty($ungrouped_leaderboards)) {
            foreach($ungrouped_leaderboards as $ungrouped_leaderboard) {
                if($category_name == 'deathless' || empty($ungrouped_leaderboard['is_deathless'])) {
                    $character_name = $ungrouped_leaderboard['character_name'];
                
                    $group_name_segments = array();
                    
                    if(!empty($ungrouped_leaderboard['is_seeded'])) {
                        $group_name_segments[] = 'seeded';
                    }
                    
                    if(!empty($ungrouped_leaderboard['is_co_op'])) {
                        $group_name_segments[] = 'co_op';
                    }
                    
                    if(!empty($ungrouped_leaderboard['is_custom'])) {
                        $group_name_segments[] = 'custom';
                    }

                    if(empty($group_name_segments)) {
                        $group_name_segments[] = 'main';
                    }
                    
                    $group_name = implode('_', $group_name_segments);
                    
                    $grouped_score_leaderboards[$group_name]['characters'][$character_name] = $ungrouped_leaderboard['lbid'];
                }
            }
        }
        
        return $grouped_score_leaderboards;
    }

    public static function getAllByCategoryFromDatabase($category_name) {           
        $leaderboards_in_category = array();

        $leaderboard_flag = '';

        switch($category_name) {
            case 'score':
                $leaderboard_flag = 'is_score_run';
                break;
            case 'speed':
                $leaderboard_flag = 'is_speedrun';
                break;
            case 'deathless':
                $leaderboard_flag = 'is_deathless';
                break;
            default:
                throw new Exception("Specified category name '{$category_name}' does not exist. It must be 'score', 'speed', or 'deathless'.");
                break;
        }
    
        return db()->getAll("
            SELECT 
                l.*,
                c.name,
            FROM leaderboards l
            JOIN characters c ON c.character_id = l.character_id
            WHERE {$leaderboard_flag} = 1
        ");
        
        return $leaderboards_in_category;
    }
    
    public static function getAllByCategoryFromCache($category_name) {
        $cache = cache('read');
    
        $leaderboard_category_hash_name = '';
        
        switch($category_name) {
            case 'score':
                $leaderboard_category_hash_name = CacheEntryNames::SCORE_LEADERBOARDS;
                break;
            case 'speed':
                $leaderboard_category_hash_name = CacheEntryNames::SPEEDRUN_LEADERBOARDS;
                break;
            case 'deathless':
                $leaderboard_category_hash_name = CacheEntryNames::DEATHLESS_LEADERBOARDS;
                break;
        }
        
        $leaderboard_names = $cache->hGetAll($leaderboard_category_hash_name);
        
        $leaderboards_in_category = array();
        
        if(!empty($leaderboard_names)) {            
            $leaderboards_in_category = $cache->hGetAllMulti($leaderboard_names);
        }
        
        return $leaderboards_in_category;
    }
    
    public static function getFancyLeaderboardName(array $leaderboard_record, $prepend_run_type = false) {
        $raw_leaderboard_name = $leaderboard_record['name'];
        
        $fancy_leaderboard_name = '';
        
        if($prepend_run_type) {
            if(!empty($leaderboard_record['is_score_run'])) {
                $fancy_leaderboard_name .= 'Score';
            }
            elseif(!empty($leaderboard_record['is_speedrun'])) {
                $fancy_leaderboard_name .= 'Speedrun';
            }
            elseif(!empty($leaderboard_record['is_deathless'])) {
                $fancy_leaderboard_name .= 'Deathless';
            }
            
            $fancy_leaderboard_name .= ' - ';
        }
        
        $second_half_name_segments = array();
        
        if(!empty($leaderboard_record['is_story_mode'])) {
            $second_half_name_segments[] = 'Story Mode';
        }
        
        if(!empty($leaderboard_record['is_all_character'])) {
            $second_half_name_segments[] = 'All Chars';
        }
        
        if(!empty($leaderboard_record['is_seeded'])) {
            $second_half_name_segments[] = 'Seeded';
        }
        
        if(!empty($leaderboard_record['is_co_op'])) {
            $second_half_name_segments[] = 'Co-op';
        }
        
        if(!empty($leaderboard_record['is_custom'])) {
            $second_half_name_segments[] = 'Custom Music';
        }        
        
        if(empty($second_half_name_segments)) {
            $second_half_name_segments[] = 'All Zones Mode';
        }
        
        $second_half_name = implode(' ', $second_half_name_segments);
        
        $fancy_leaderboard_name .= $second_half_name;
        
        return $fancy_leaderboard_name;
    }
    
    public static function getLeaderboardRecord($lbid) {
        $leaderboard_entry_name = CacheEntryNames::generateLeaderboardName($lbid);
        
        return cache('read')->hGetAll($leaderboard_entry_name);
    }
    
    public static function getHighestZoneLevelFromDetails($details) {
        $details_split = explode('0000000', $details);
                            
        $highest_zone = (int)$details_split[0];
        $highest_level = (int)str_replace('0', '', $details_split[1]);
        
        return array(
            'highest_zone' => $highest_zone,
            'highest_level' => $highest_level
        );
    }
    
    public static function getRankingsFromCache($lbid, $leaderboard_type, $page_number = 1, $rows_per_page = 100) {    
        $cache = cache('read');
    
        $resultset = new Redis(CacheEntryNames::generateLeaderboardName($lbid), $cache);
        
        $resultset->setEntriesName(CacheEntryNames::generateLeaderboardEntriesName($lbid));
        $resultset->setFilterName(CacheEntryNames::generateLeaderboardEntriesFilterName($lbid));
        
        $resultset->enableTotalRecordCount();
        $resultset->setRowsPerPage($rows_per_page);    
        $resultset->setPageNumber($page_number);
        
        $resultset->addProcessorFunction(function($result_data, $leaderboard_type) {
            $processed_data = array();
        
            if(!empty($result_data)) {    
                $cache = cache('read');
                
                $grouped_steam_user_data = Rankings::getSteamUsersFromResultData($result_data, $leaderboard_type);
                
                foreach($result_data as $result_row) {
                    $steamid = $result_row['steamid'];

                    $personaname = NULL;
                    $twitch_username = NULL;
                    $nico_nico_url = NULL;
                    $hitbox_username = NULL;
                    $twitter_username = NULL;
                    $website = NULL;
                    
                    if(!empty($grouped_steam_user_data[$steamid])) {
                        $steam_user_record = $grouped_steam_user_data[$steamid];
                        
                        $personaname = $steam_user_record['personaname'];
                        $twitch_username = $steam_user_record['twitch_username'];
                        $nico_nico_url = $steam_user_record['nico_nico_url'];
                        $hitbox_username = $steam_user_record['hitbox_username'];
                        $twitter_username = $steam_user_record['twitter_username'];
                        $website = $steam_user_record['website'];
                    }
                    
                    $processed_data_row = array(
                        'steamid' => $result_row['steamid'],
                        'rank' => $result_row['rank'],
                        'personaname' => $personaname,
                        'social_media' => array(
                            'twitch_username' => $twitch_username,
                            'nico_nico_url' => $nico_nico_url,
                            'hitbox_username' => $hitbox_username,
                            'twitter_username' => $twitter_username,
                            'website' => $website
                        )
                    );
                    
                    switch($leaderboard_type) {
                        case 'score':
                            $details = $result_row['details'];
                            
                            $highest_zone_level = self::getHighestZoneLevelFromDetails($details);
                        
                            $is_win = 'No';
                            
                            if($details == "0400000006000000") {
                                $is_win = "Yes";
                            }
                        
                            $processed_data_row['score'] = $result_row['score'];
                            $processed_data_row['highest_zone'] = $highest_zone_level['highest_zone'];
                            $processed_data_row['highest_level'] = $highest_zone_level['highest_level'];
                            $processed_data_row['is_win'] = $is_win;
                            break;
                        case 'speed':
                            $time = $result_row['time'] / 1000;
                            
                            $minutes = (int)($time / 60);
                            
                            $seconds = (int)($time - ($minutes * 60));
                            
                            $milliseconds = round($time - ($minutes * 60) - $seconds, 2, PHP_ROUND_HALF_DOWN);
                            $milliseconds = str_replace('0.', '', $milliseconds);
                            
                            $processed_data_row['time'] = str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT) . ".{$milliseconds}";
                            break;
                        case 'deathless':
                            $details = $result_row['details'];
                            
                            $highest_zone_level = self::getHighestZoneLevelFromDetails($details);
                        
                            $is_win = 'No';
                            
                            if($details == "0400000006000000") {
                                $is_win = "Yes";
                            }
                            
                            $score = $result_row['score'];
                            $wins = 0;
                            
                            if($score > 99) {
                                $wins = (int)($score / 100);
                            }
                            
                            $processed_data_row['wins'] = $wins;
                            $processed_data_row['highest_zone'] = $highest_zone_level['highest_zone'];
                            $processed_data_row['highest_level'] = $highest_zone_level['highest_level'];
                            $processed_data_row['is_win'] = $is_win;
                            break;
                        default:
                            throw new Exception("Leaderboard type '{$leaderboard_type}' is not valid. Please specify only 'score', 'speed', or 'deathless'.");
                            break;
                    }
                    
                    $processed_data[] = $processed_data_row;
                }
            }
            
            return $processed_data;
        }, array(
            'leaderboard_type' => $leaderboard_type
        ));
        
        return $resultset;
    }
    
    public static function reloadBlackListCache() {
        $blacklisted_leaderboards = db()->getColumn("
            SELECT
                l.lbid
            FROM leaderboards_blacklist lb
            JOIN leaderboards l ON l.leaderboard_id = lb.leaderboard_id
        ");
    
        if(!empty($blacklisted_leaderboards)) {
            $transaction = cache('write')->transaction();
            
            $transaction->delete(CacheEntryNames::LEADERBOARDS_BLACKLIST);
            
            foreach($blacklisted_leaderboards as $blacklisted_leaderboard_lbid) {
                $transaction->hSet(CacheEntryNames::LEADERBOARDS_BLACKLIST, $blacklisted_leaderboard_lbid, $blacklisted_leaderboard_lbid);
            }
            
            $transaction->commit();
        }
    }
}