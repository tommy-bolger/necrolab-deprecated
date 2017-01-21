<?php
namespace Modules\Necrolab\Models;

use \Exception;
use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;
use \Modules\Necrolab\Objects\CacheEntryNames;

class Rankings {
    public static function getSteamUsersFromResultData(array $result_data) {
        $steam_user_record_names = array();
        
        if(!empty($result_data)) {
            foreach($result_data as $result_row) {
                $steam_user_record_names[] = CacheEntryNames::generateSteamUserEntryName($result_row['steamid']);
            }
        }
        
        $grouped_steam_user_data = array();
        
        if(!empty($steam_user_record_names)) {
            $steam_user_data = cache('read')->hGetAllMulti($steam_user_record_names);
            
            foreach($steam_user_data as $steam_user_record) {
                if(!empty($steam_user_record)) {
                    $grouped_steam_user_data[$steam_user_record['steamid']] = $steam_user_record;
                }
            }
        }
        
        return $grouped_steam_user_data;
    }

    protected static function processResultsetDisplay($data_type, array $result_data) {
        switch($data_type) {
            case 'score':
            case 'speed':
            case 'deathless_score':
                break;
            default:
                throw new Exception("Data type '{$data_type}' is invalid. Please specify only 'score', 'speed', or 'deathless'.");
                break;
        }
    
        $processed_data = array();
        
        if(!empty($result_data)) {
            $grouped_steam_user_data = static::getSteamUsersFromResultData($result_data);
            
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
                
                $cadence_rank = NULL;
                
                if(isset($result_row["cadence_{$data_type}_rank"])) {
                    $cadence_rank = $result_row["cadence_{$data_type}_rank"];
                }
                
                $cadence_points = NULL;
                
                if(isset($result_row["cadence_{$data_type}_rank_points"])) {
                    $cadence_points = $result_row["cadence_{$data_type}_rank_points"];
                }
                
                $bard_rank = NULL;
                
                if(isset($result_row["bard_{$data_type}_rank"])) {
                    $bard_rank = $result_row["bard_{$data_type}_rank"];
                }
                
                $bard_points = NULL;
                
                if(isset($result_row["bard_{$data_type}_rank_points"])) {
                    $bard_points = $result_row["bard_{$data_type}_rank_points"];
                }
                
                $monk_rank = NULL;
                
                if(isset($result_row["monk_{$data_type}_rank"])) {
                    $monk_rank = $result_row["monk_{$data_type}_rank"];
                }
                
                $monk_points = NULL;
                
                if(isset($result_row["monk_{$data_type}_rank_points"])) {
                    $monk_points = $result_row["monk_{$data_type}_rank_points"];
                }
                
                $aria_rank = NULL;
                
                if(isset($result_row["aria_{$data_type}_rank"])) {
                    $aria_rank = $result_row["aria_{$data_type}_rank"];
                }
                
                $aria_points = NULL;
                
                if(isset($result_row["aria_{$data_type}_rank_points"])) {
                    $aria_points = $result_row["aria_{$data_type}_rank_points"];
                }
                
                $bolt_rank = NULL;
                
                if(isset($result_row["bolt_{$data_type}_rank"])) {
                    $bolt_rank = $result_row["bolt_{$data_type}_rank"];
                }
                
                $bolt_points = NULL;
                
                if(isset($result_row["bolt_{$data_type}_rank_points"])) {
                    $bolt_points = $result_row["bolt_{$data_type}_rank_points"];
                }
                
                $dove_rank = NULL;
                
                if(isset($result_row["dove_{$data_type}_rank"])) {
                    $dove_rank = $result_row["dove_{$data_type}_rank"];
                }
                
                $dove_points = NULL;
                
                if(isset($result_row["dove_{$data_type}_rank_points"])) {
                    $dove_points = $result_row["dove_{$data_type}_rank_points"];
                }
                
                $eli_rank = NULL;
                
                if(isset($result_row["eli_{$data_type}_rank"])) {
                    $eli_rank = $result_row["eli_{$data_type}_rank"];
                }
                
                $eli_points = NULL;
                
                if(isset($result_row["eli_{$data_type}_rank_points"])) {
                    $eli_points = $result_row["eli_{$data_type}_rank_points"];
                }
                
                $melody_rank = NULL;
                
                if(isset($result_row["melody_{$data_type}_rank"])) {
                    $melody_rank = $result_row["melody_{$data_type}_rank"];
                }
                
                $melody_points = NULL;
                
                if(isset($result_row["melody_{$data_type}_rank_points"])) {
                    $melody_points = $result_row["melody_{$data_type}_rank_points"];
                }
                
                $dorian_rank = NULL;
                
                if(isset($result_row["dorian_{$data_type}_rank"])) {
                    $dorian_rank = $result_row["dorian_{$data_type}_rank"];
                }
                
                $dorian_points = NULL;
                
                if(isset($result_row["dorian_{$data_type}_rank_points"])) {
                    $dorian_points = $result_row["dorian_{$data_type}_rank_points"];
                }
                
                $coda_rank = NULL;
                
                if(isset($result_row["coda_{$data_type}_rank"])) {
                    $coda_rank = $result_row["coda_{$data_type}_rank"];
                }
                
                $coda_points = NULL;
                
                if(isset($result_row["coda_{$data_type}_rank_points"])) {
                    $coda_points = $result_row["coda_{$data_type}_rank_points"];
                }
                
                $story_rank = NULL;
                
                if($data_type != 'deathless_score' && isset($result_row["story_{$data_type}_rank"])) {
                    $story_rank = $result_row["story_{$data_type}_rank"];
                }
                
                $story_points = NULL;
                
                if($data_type != 'deathless_score' && isset($result_row["story_{$data_type}_rank_points"])) {
                    $story_points = $result_row["story_{$data_type}_rank_points"];
                }
                
                $all_rank = NULL;
                
                if($data_type != 'deathless_score' && isset($result_row["all_{$data_type}_rank"])) {
                    $all_rank = $result_row["all_{$data_type}_rank"];
                }
                
                $all_points = NULL;
                
                if($data_type != 'deathless_score' && isset($result_row["all_{$data_type}_rank_points"])) {
                    $all_points = $result_row["all_{$data_type}_rank_points"];
                }
                
                $total_points = NULL;
                
                if(isset($result_row["{$data_type}_rank_points_total"])) {
                    $total_points = $result_row["{$data_type}_rank_points_total"];
                }

                $processed_row = array(
                    'steamid' => $result_row['steamid'],
                    'rank' => $result_row["{$data_type}_rank"],
                    'personaname' => $personaname,
                    'social_media' => array(
                        'twitch_username' => $twitch_username,
                        'nico_nico_url' => $nico_nico_url,
                        'hitbox_username' => $hitbox_username,
                        'twitter_username' => $twitter_username,
                        'website' => $website
                    ),
                    'cadence_rank' => $cadence_rank,
                    'cadence_points' => $cadence_points,
                    'bard_rank' => $bard_rank,
                    'bard_points' => $bard_points,
                    'monk_rank' => $monk_rank,
                    'monk_points' => $monk_points,
                    'aria_rank' => $aria_rank,
                    'aria_points' => $aria_points,
                    'bolt_rank' => $bolt_rank,
                    'bolt_points' => $bolt_points,
                    'dove_rank' => $dove_rank,
                    'dove_points' => $dove_points,
                    'eli_rank' => $eli_rank,
                    'eli_points' => $eli_points,
                    'melody_rank' => $melody_rank,
                    'melody_points' => $melody_points,
                    'dorian_rank' => $dorian_rank,
                    'dorian_points' => $dorian_points,
                    'coda_rank' => $coda_rank,
                    'coda_points' => $coda_points,
                    'story_rank' => $story_rank,
                    'story_points' => $story_points,
                    'all_rank' => $all_rank,
                    'all_points' => $all_points,
                    'total_points' => $total_points
                );
                
                if($data_type == 'deathless_score') {
                    unset($processed_row['story_rank']);
                    unset($processed_row['story_points']);
                    unset($processed_row['all_rank']);
                    unset($processed_row['all_points']);
                }
                
                $processed_data[] = $processed_row;
            }
        }
        
        return $processed_data;
    }
}