<?php
namespace Modules\Necrolab\Models\Rankings;

use \DateTime;
use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class Rankings
extends Necrolab {
    protected static $rankings = array();

    protected static function load(DateTime $date) {}

    public static function get(DateTime $date) {
        static::load($date);
        
        $date_formatted = $date->format('Y-m-d');
        
        $ranking = array();
        
        if(isset(static::$rankings[$date_formatted])) {
            $ranking = static::$rankings[$date_formatted];
        }
        
        return $ranking;
    }

    public static function getRankings($page_number = 1, $rows_per_page = 100) {
        $resultset = static::getLatestRankingsResultset();
    
        $resultset->enableTotalRecordCount();
        $resultset->setRowsPerPage($rows_per_page);    
        $resultset->setPageNumber($page_number);
        
        return $resultset;
    }

    protected static function processCategoryResultset($data_type, $entry_class_name, array $result_data) {
        $entry_object_name = '';
    
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
                
                $entry_object = new $entry_class_name();
                $entry_object->setPropertiesFromArray($result_row);

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
                    'cadence_rank' => $entry_object->{"cadence_{$data_type}_rank"},
                    'cadence_points' => $entry_object->{"cadence_{$data_type}_rank_points"},
                    'bard_rank' => $entry_object->{"bard_{$data_type}_rank"},
                    'bard_points' => $entry_object->{"bard_{$data_type}_rank_points"},
                    'monk_rank' => $entry_object->{"monk_{$data_type}_rank"},
                    'monk_points' => $entry_object->{"monk_{$data_type}_rank_points"},
                    'aria_rank' => $entry_object->{"aria_{$data_type}_rank"},
                    'aria_points' => $entry_object->{"aria_{$data_type}_rank_points"},
                    'bolt_rank' => $entry_object->{"bolt_{$data_type}_rank"},
                    'bolt_points' => $entry_object->{"bolt_{$data_type}_rank_points"},
                    'dove_rank' => $entry_object->{"dove_{$data_type}_rank"},
                    'dove_points' => $entry_object->{"dove_{$data_type}_rank_points"},
                    'eli_rank' => $entry_object->{"eli_{$data_type}_rank"},
                    'eli_points' => $entry_object->{"eli_{$data_type}_rank_points"},
                    'melody_rank' => $entry_object->{"melody_{$data_type}_rank"},
                    'melody_points' => $entry_object->{"melody_{$data_type}_rank_points"},
                    'dorian_rank' => $entry_object->{"dorian_{$data_type}_rank"},
                    'dorian_points' => $entry_object->{"dorian_{$data_type}_rank_points"},
                    'coda_rank' => $entry_object->{"coda_{$data_type}_rank"},
                    'coda_points' => $entry_object->{"coda_{$data_type}_rank_points"}
                );
                
                if($data_type != 'deathless_score') {
                    $processed_row['story_rank'] = $entry_object->{"story_{$data_type}_rank"};
                    $processed_row['story_points'] = $entry_object->{"story_{$data_type}_rank_points"};
                    $processed_row['all_rank'] = $entry_object->{"all_{$data_type}_rank"};
                    $processed_row['all_points'] = $entry_object->{"all_{$data_type}_rank_points"};
                }
                
                $processed_row['total_points'] = $entry_object->{"{$data_type}_rank_points_total"};
                
                $processed_data[] = $processed_row;
            }
        }
        
        return $processed_data;
    }
    
    public static function processPowerResultSet($entry_class_name, array $result_data) {
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
                
                $power_ranking_entry = new $entry_class_name();
                $power_ranking_entry->setPropertiesFromArray($result_row);
            
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
                    'score_rank' => $power_ranking_entry->score_rank,
                    'score_rank_points_total' => $power_ranking_entry->score_rank_points_total,
                    'speed_rank' => $power_ranking_entry->speed_rank,
                    'speed_rank_points_total' => $power_ranking_entry->speed_rank_points_total,
                    'deathless_score_rank' => $power_ranking_entry->deathless_score_rank,
                    'deathless_score_rank_points_total' => $power_ranking_entry->deathless_score_rank_points_total,
                    'total_points' => $power_ranking_entry->total_points
                );
            }
        }
        
        return $processed_data;
    }
    
    public static function getLastRefreshed() {
        return false;
    }
}