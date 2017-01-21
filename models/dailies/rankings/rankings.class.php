<?php
namespace Modules\Necrolab\Models\Dailies\Rankings;

use \DateTime;
use \Modules\Necrolab\Models\Necrolab;

class Rankings
extends Necrolab {    
    protected static $rankings = array();

    protected static function load($daily_ranking_day_type_id, DateTime $date) {}

    public static function get($daily_ranking_day_type_id, DateTime $date) {
        static::load($daily_ranking_day_type_id, $date);
        
        $date_formatted = $date->format('Y-m-d');
        
        $ranking = array();
        
        if(isset(static::$rankings[$date_formatted][$daily_ranking_day_type_id])) {
            $ranking = static::$rankings[$date_formatted][$daily_ranking_day_type_id];
        }
        
        return $ranking;
    }

    public static function getRankingsResultset($number_of_days = NULL) {}
    
    protected static function getSteamUsersFromResultData($result_data) {}
    
    public static function processRankingsResulset($result_data) {                
        $processed_data = array();
        
        if(!empty($result_data)) {    
            $cache = cache('read');
        
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
                    
                    if(!empty($steam_user_record['personaname'])) {
                        $personaname = $steam_user_record['personaname'];
                    }
                    
                    if(!empty($steam_user_record['twitch_username'])) {
                        $twitch_username = $steam_user_record['twitch_username'];
                    }
                    
                    if(!empty($steam_user_record['nico_nico_url'])) {
                        $nico_nico_url = $steam_user_record['nico_nico_url'];
                    }
                    
                    if(!empty($steam_user_record['hitbox_username'])) {
                        $hitbox_username = $steam_user_record['hitbox_username'];
                    }
                    
                    if(!empty($steam_user_record['twitter_username'])) {
                        $twitter_username = $steam_user_record['twitter_username'];
                    }
                    
                    if(!empty($steam_user_record['website'])) {
                        $website = $steam_user_record['website'];
                    }
                }
                
                $processed_row = array(
                    'steamid' => $steamid,
                    'rank' => $result_row['rank'],
                    'social_media' => array(
                        'twitch_username' => $twitch_username,
                        'nico_nico_url' => $nico_nico_url,
                        'hitbox_username' => $hitbox_username,
                        'twitter_username' => $twitter_username,
                        'website' => $website
                    ),
                    'personaname' => $personaname
                );
                
                $default_record = array(
                    'first_place_ranks' => 0,
                    'top_5_ranks' => 0,
                    'top_10_ranks' => 0,
                    'top_20_ranks' => 0,
                    'top_50_ranks' => 0,
                    'top_100_ranks' => 0,
                    'total_points' => 0,
                    'points_per_day' => 0,
                    'total_dailies' => 0,
                    'total_wins' => 0,
                    'average_rank' => 0
                );
                
                $merged_record = array_merge($default_record, $result_row);
            
                $processed_data[] = array(
                    'steamid' => $result_row['steamid'],
                    'rank' => $result_row['rank'],
                    'personaname' => $personaname,
                    'social_media' => array(
                        'twitch_username' => $twitch_username,
                        'nico_nico_url' => $nico_nico_url,
                        'hitbox_username' => $hitbox_username,
                        'twitter_username' => $twitter_username,
                        'website' => $website
                    ),
                    'first_place_ranks' => $merged_record['first_place_ranks'],
                    'top_5_ranks' => $merged_record['top_5_ranks'],
                    'top_10_ranks' => $merged_record['top_10_ranks'],
                    'top_20_ranks' => $merged_record['top_20_ranks'],
                    'top_50_ranks' => $merged_record['top_50_ranks'],
                    'top_100_ranks' => $merged_record['top_100_ranks'],
                    'total_points' => $merged_record['total_points'],
                    'points_per_day' => $merged_record['points_per_day'],
                    'total_dailies' => $merged_record['total_dailies'],
                    'total_wins' => $merged_record['total_wins'],
                    'average_rank' => $merged_record['average_rank']
                );
            }
        }
        
        return $processed_data;
    }
}