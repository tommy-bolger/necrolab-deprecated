<?php
namespace Modules\Necrolab\Models;

use \DateTime;
use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;
use \Modules\Necrolab\Objects\CacheEntryNames;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\Rankings;

class DailyRankingSeasons {
    protected static $latest_season = array();

    public static function getLatestSeasonFromDatabase() {
        if(empty(self::$latest_season)) {
            self::$latest_season = db()->getRow("
                SELECT
                    daily_ranking_season_id,
                    season_number,
                    start_date,
                    end_date
                FROM daily_ranking_seasons
                WHERE is_latest = 1
            ");
        }
    
        return self::$latest_season;
    }
    
    public static function getLatestSeasonFromCache() {
        if(empty(self::$latest_season)) {
            self::$latest_season = cache('read')->hGetAll(CacheEntryNames::DAILY_SEASON_RANKINGS);
        }
    
        return self::$latest_season;
    }
    
    public static function getSeasonFancyName($season_number = NULL) {
        $season = NULL;
    
        if(empty($season_number)) {
            $season = self::getLatestSeasonFromCache();
        }
        else {
            
        }
        
        $start_date = new DateTime($season['start_date']);
        $end_date = new DateTime($season['end_date']);
        
        return "Season {$season['season_number']}: {$start_date->format('m/d/Y')} - {$end_date->format('m/d/Y')}";
    }

    protected static function getLatestSeasonQuery() {
        return "
            SELECT
                drse.rank,
                su.personaname,
                drse.first_place_ranks,
                drse.top_5_ranks,
                drse.top_10_ranks,
                drse.top_20_ranks,
                drse.top_50_ranks,
                drse.top_100_ranks,
                drse.total_points,
                drse.points_per_day,
                drse.total_dailies,
                drse.total_wins,
                drse.average_place                
            FROM daily_ranking_seasons drs
            JOIN daily_ranking_season_entries drse ON drse.daily_ranking_season_id = drs.daily_ranking_season_id
            JOIN steam_users su ON su.steam_user_id = drse.steam_user_id
            WHERE drs.is_latest = 1
        ";
    }

    public static function getLatestRankingsFromDatabase($page_number = 1, $rows_per_page = 100) {
        $resultset = new SQL('daily_ranking_seasons');
        
        $query = self::getLatestSeasonQuery();
        
        $resultset->setBaseQuery("
            {$query}
            {{WHERE_CRITERIA}}
        ");
        
        //Set default sort criteria
        $resultset->setSortCriteria('drse.rank', 'ASC');
        
        $resultset->enableTotalRecordCount();
        
        //Set default rows per page
        $resultset->setRowsPerPage($rows_per_page);    
        
        //Set the default page number
        $resultset->setPageNumber($page_number);  
        
        return $resultset;
    }
    
    public static function getLatestRankingsFromCache($page_number = 1, $rows_per_page = 100) {
        $resultset = new Redis(CacheEntryNames::DAILY_SEASON_RANKINGS, cache('read'));
        
        $resultset->setEntriesName(CacheEntryNames::DAILY_SEASON_RANKING_ENTRIES);
        $resultset->setFilterName(CacheEntryNames::DAILY_SEASON_RANKING_ENTRIES_FILTER);
        
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
    
    public static function deleteSeasonInCache() {
        cache('write')->reval(file_get_contents(Necrolab::getLuaScriptPath() . '/delete_daily_ranking_season.lua'), array(
            CacheEntryNames::DAILY_SEASON_RANKINGS,
            CacheEntryNames::DAILY_SEASON_RANKING_ENTRIES,
            CacheEntryNames::DAILY_SEASON_RANKING_ENTRIES_FILTER
        ));
        
        $latest_daily_season = db()->getRow("
            SELECT *
            FROM daily_rankng_seasons
            WHERE is_latest = 1
        ");
        
        if(!empty($latest_daily_season)) {
            cache('write')->hMSet(CacheEntryNames::DAILY_SEASON_RANKINGS, $latest_daily_season);
        }
    }
    
    public static function loadLatestSeasonIntoCache($cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
    
        $latest_season = self::getLatestSeason();
        
        if(!empty($latest_season)) {
            $cache->hMset(CacheEntryNames::DAILY_SEASON_RANKINGS, $latest_season);
        }
    }
    
    public static function loadLatestSeasonEntriesIntoCache($cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
    
        $latest_season = self::getLatestSeason();
    
        if(!empty($latest_season)) {            
            $query = self::getLatestSeasonQuery();
        
            $daily_season_rankings = db()->prepareExecuteQuery("
                {$query}
                ORDER BY dre.rank ASC
            ");

            $transaction = $cache->transaction();

            while($daily_season_ranking = $daily_season_rankings->fetch(PDO::FETCH_ASSOC)) {
                $personaname = $daily_season_ranking['personaname'];
                $steamid = $daily_season_ranking['steamid'];
                
                unset($daily_season_ranking['personaname']);
            
                $daily_season_entry_name = CacheEntryNames::generateDailyRankingSeasonEntryName($steamid);
                
                $transaction->hMSet($daily_season_entry_name, $daily_season_ranking);
                $transaction->zAdd(CacheEntryNames::DAILY_SEASON_RANKING_ENTRIES, $daily_season_ranking['rank'], $daily_season_entry_name);
                
                if(!empty($personaname)) {
                    $transaction->hSet(CacheEntryNames::DAILY_SEASON_RANKING_ENTRIES_FILTER, $personaname, $daily_season_entry_name);
                }
            }
            
            $transaction->commit();
        }
    }
    
    public static function loadEnrollmentInCache($cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $users_enrolled_in_season = db()->getColumn("
            SELECT su.steamid
            FROM daily_ranking_seasons drs
            JOIN daily_ranking_season_enrollment drse ON drse.daily_ranking_season_id = drs.daily_ranking_season_id
            JOIN steam_users su ON su.steam_user_id = drse.steam_user_id
            WHERE drs.is_latest = 1
        ");
        
        if(!empty($users_enrolled_in_season)) {
            $transaction = $cache->transaction();
        
            foreach($users_enrolled_in_season as $steamid) {
                $transaction->hSet(CacheEntryNames::DAILY_SEASON_RANKING_ENROLLMENT, $steamid, CacheEntryNames::generateSteamUserEntryName($steamid));
            }
            
            $transaction->commit();
        }
    }
    
    public static function setSeasonAsLatest($daily_ranking_season_id) {
        db()->update('daily_ranking_seasons', array(
            'is_latest' => 0
        ));
        
        db()->update('daily_ranking_seasons', array(
            'is_latest' => 1
        ), array(
            'daily_ranking_season_id' => $daily_ranking_season_id
        ));
    }
    
    public static function setLastSeasonAsLatest() {
        $last_season = db()->getRow("
            SELECT 
                daily_ranking_season_id,
                is_latest
            FROM daily_ranking_seasons
            ORDER BY start_date DESC
            LIMIT 1
        ");
        
        if(!empty($last_season) && empty($last_season['is_latest'])) {
            $daily_ranking_season_id = $last_season['daily_ranking_season_id'];
            
            db()->update('daily_ranking_seasons', array(
                'is_latest' => 0
            ));
        
            db()->update('daily_ranking_seasons', array(
                'is_latest' => 1
            ), array(
                'daily_ranking_season_id' => $daily_ranking_season_id
            ));
        }
    }
    
    public static function reorderSeasons() {
        $daily_ranking_seasons = db()->getAll("
            SELECT
                daily_ranking_season_id,
                season_number,
                is_latest
            FROM daily_ranking_seasons
            ORDER BY start_date ASC
        ");
        
        if(!empty($daily_ranking_seasons)) {
            $season_number = 1;
            
            //Get the latest season ordered by date
            end($daily_ranking_seasons);
            
            $latest_season = current($daily_ranking_seasons);
            $latest_season_id = $latest_season['daily_ranking_season_id'];
            
            reset($daily_ranking_seasons);
            
            foreach($daily_ranking_seasons as $daily_ranking_season) {
                $daily_ranking_season_id = $daily_ranking_season['daily_ranking_season_id'];
                $is_latest = 0;
                
                if($daily_ranking_season_id == $latest_season_id) {
                    $is_latest = 1;
                }
                
                db()->update('daily_ranking_seasons', array(
                    'season_number' => $season_number,
                    'is_latest' => $is_latest
                ), array(
                    'daily_ranking_season_id' => $daily_ranking_season_id
                ));
            
                $season_number += 1;
            }
        }
    }
}