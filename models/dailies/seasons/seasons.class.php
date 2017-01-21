<?php
namespace Modules\Necrolab\Models\Dailies\Seasons;

use \DateTime;
use \Modules\Necrolab\Models\Necrolab;

class Seasons
extends Necrolab {
    protected static $seasons = array();

    protected static $latest_season = array();
    
    protected static function loadAll() {}
    
    protected static function loadLatest() {
        if(empty(static::$seasons)) {
            static::loadAll();
            
            if(!empty(static::$seasons)) {
                foreach(static::$seasons as $season)  {
                    if($season['is_latest'] == 1) {
                        static::$latest_season = $season;
                        
                        break;
                    }
                }
            }
        }
    }
    
    public static function getAll() {
        static::loadAll();

        return static::$seasons;
    }
    
    public static function getLatest() {
        static::loadLatest();
        
        return static::$latest_season;
    }
    
    public static function get($season_number) {
        static::loadAll();
        
        $matched_season = NULL;
        
        if(!empty(static::$seasons)) {
            foreach(static::$seasons as $season)  {
                if($season['season_number'] == 1) {
                    $matched_season = $season;
                    
                    break;
                }
            }
        }
        
        return $matched_season;
    }
    
    public static function dateInRange(DateTime $date, DateTime $start_date, DateTime $end_date) {
        $date_in_range = false;
        
        if($date >= $start_date && $date <= $end_date) {
            $date_in_range = true;
        }
        
        return $date_in_range;
    }
    
    public static function getActive(DateTime $season_date) {
        static::loadAll();
        
        $active_season = array();
        
        if(!empty(static::$seasons)) {
            foreach(static::$seasons as $season) {
                if(static::dateInRange($season_date, new DateTime($season['start_date']), new DateTime($season['end_date']))) {
                    $active_season = $season;
                    
                    break;
                }
            }
        }
        
        return $active_season;
    }
    
    public static function getFancyName($season) {        
        $start_date = new DateTime($season['start_date']);
        $end_date = new DateTime($season['end_date']);
        
        return "Season {$season['season_number']}: {$start_date->format('m/d/Y')} - {$end_date->format('m/d/Y')}";
    }
    
    public static function getRankingsResultset($season_number) {}
    
    protected static function getSteamUsersFromResultData($result_data) {}
    
    public static function getRankings($season_number, $page_number = 1, $rows_per_page = 100) {
        $resultset = static::getRankingsResultSet($season_number);
        
        $resultset->enableTotalRecordCount();
        $resultset->setRowsPerPage($rows_per_page);    
        $resultset->setPageNumber($page_number);
        
        $resultset->addProcessorFunction(function($result_data) {
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
                        
                        $personaname = $steam_user_record['personaname'];
                        $twitch_username = $steam_user_record['twitch_username'];
                        $nico_nico_url = $steam_user_record['nico_nico_url'];
                        $hitbox_username = $steam_user_record['hitbox_username'];
                        $twitter_username = $steam_user_record['twitter_username'];
                        $website = $steam_user_record['website'];
                    }
                
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
                        'first_place_ranks' => $result_row['first_place_ranks'],
                        'top_5_ranks' => $result_row['top_5_ranks'],
                        'top_10_ranks' => $result_row['top_10_ranks'],
                        'top_20_ranks' => $result_row['top_20_ranks'],
                        'top_50_ranks' => $result_row['top_50_ranks'],
                        'top_100_ranks' => $result_row['top_100_ranks'],
                        'total_points' => $result_row['total_points'],
                        'points_per_day' => $result_row['points_per_day'],
                        'total_dailies' => $result_row['total_dailies'],
                        'total_wins' => $result_row['total_wins'],
                        'average_rank' => $result_row['average_rank']
                    );
                }
            }
            
            return $processed_data;
        });
        
        return $resultset;
    }
    
    public static function generateRankings($season_number) {}
}