<?php
namespace Modules\Necrolab\Models;

use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;
use \Modules\Necrolab\Objects\CacheEntryNames;
use \Modules\Necrolab\Models\Rankings;

class PowerRankings {
    public static function getLatestRankingsFromDatabase($page_number = 1, $rows_per_page = 100) {
        $resultset = new SQL('power_rankings');
        
        $resultset->setBaseQuery("
            SELECT
                pre.rank,
                su.personaname,
                pre.score_rank,
                pre.score_rank_points_total,
                pre.deathless_score_rank,
                pre.deathless_score_rank_points_total,
                pre.speed_rank,
                pre.speed_rank_points_total,
                pre.total_points,
                su.steamid,
                pre.power_ranking_entry_id,
                su.twitch_username,
                su.nico_nico_url,
                su.hitbox_username,
                su.twitter_username,
                su.website
            FROM power_rankings pr
            JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
            JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
            WHERE pr.latest = 1
            {{WHERE_CRITERIA}}
        ");

        $resultset->setSortCriteria('pre.rank', 'ASC');  
        $resultset->enableTotalRecordCount();
        $resultset->setRowsPerPage($rows_per_page);    
        $resultset->setPageNumber($page_number);  
        
        return $resultset;
    }
    
    public static function getLatestRankingsFromCache($page_number = 1, $rows_per_page = 100) {
        $cache = cache('read');
    
        $resultset = new Redis(CacheEntryNames::POWER_RANKING, $cache);
        
        $resultset->setEntriesName(CacheEntryNames::POWER_RANKING_ENTRY);
        $resultset->setFilterName(CacheEntryNames::POWER_RANKING_ENTRIES_FILTER);
        
        $resultset->enableTotalRecordCount();
        $resultset->setRowsPerPage($rows_per_page);    
        $resultset->setPageNumber($page_number);
        
        $resultset->addProcessorFunction(function($result_data) {
            $processed_data = array();
        
            if(!empty($result_data)) {    
                $cache = cache('read');
            
                $grouped_steam_user_data = Rankings::getSteamUsersFromResultData($result_data);
                
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
                    
                    $score_rank = NULL;
                    
                    if(isset($result_row['score_rank'])) {
                        $score_rank = $result_row['score_rank'];
                    }
                    
                    $score_rank_points_total = NULL;
                    
                    if(isset($result_row['score_rank_points_total'])) {
                        $score_rank_points_total = $result_row['score_rank_points_total'];
                    }
                    
                    $speed_rank = NULL;
                    
                    if(isset($result_row['speed_rank'])) {
                        $speed_rank = $result_row['speed_rank'];
                    }
                    
                    $speed_rank_points_total = NULL;
                    
                    if(isset($result_row['speed_rank_points_total'])) {
                        $speed_rank_points_total = $result_row['speed_rank_points_total'];
                    }
                    
                    $deathless_score_rank = NULL;
                    
                    if(isset($result_row['deathless_score_rank'])) {
                        $deathless_score_rank = $result_row['deathless_score_rank'];
                    }
                    
                    $deathless_score_rank_points_total = NULL;
                    
                    if(isset($result_row['deathless_score_rank_points_total'])) {
                        $deathless_score_rank_points_total = $result_row['deathless_score_rank_points_total'];
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
                        'score_rank' => $score_rank,
                        'score_rank_points_total' => $score_rank_points_total,
                        'speed_rank' => $speed_rank,
                        'speed_rank_points_total' => $speed_rank_points_total,
                        'deathless_score_rank' => $deathless_score_rank,
                        'deathless_score_rank_points_total' => $deathless_score_rank_points_total,
                        'total_points' => $result_row['total_points']
                    );
                }
            }
            
            return $processed_data;
        });
        
        return $resultset;
    }
}