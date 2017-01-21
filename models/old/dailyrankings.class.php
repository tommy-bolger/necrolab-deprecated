<?php
namespace Modules\Necrolab\Models;

use \PDO;
use \Exception;
use \DateTime;
use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;
use \Modules\Necrolab\Objects\CacheEntryNames;
use \Modules\Necrolab\Models\Necrolab;

class DailyRankings {
    protected static $day_types = array();

    public static function getDayTypesFromDatabase($enabled = true) {
        assert('is_bool($enabled)');
        
        return db()->getAll("
            SELECT *
            FROM daily_ranking_day_types
            WHERE enabled = :enabled
        ", array(
            ':enabled' => (int)$enabled
        ));
    }
    
    public static function getDayTypesFromCache() {   
        if(empty(self::$day_types)) {
            self::$day_types = cache('read')->hGetAll(CacheEntryNames::DAILY_RANKING_DAY_TYPES);
        }
    
        return self::$day_types;
    }

    protected static function getLatestRankingsQuery() {
        return "
            SELECT
                dre.rank,
                su.personaname,
                dre.first_place_ranks,
                dre.top_5_ranks,
                dre.top_10_ranks,
                dre.top_20_ranks,
                dre.top_50_ranks,
                dre.top_100_ranks,
                dre.total_points,
                dre.points_per_day,
                dre.total_dailies,
                dre.total_wins,
                dre.average_place,
                su.steamid
            FROM daily_rankings dr
            JOIN daily_ranking_entries dre ON dre.daily_ranking_id = dr.daily_ranking_id
            JOIN steam_users su ON su.steam_user_id = dre.steam_user_id
            WHERE dr.latest = 1
        ";
    }

    public static function getLatestRankingsFromDatabase($page_number = 1, $rows_per_page = 100) {
        $resultset = NULL;
    
        if(!Framework::getInstance()->enable_cache) {
            $resultset = new SQL('daily_rankings');
            
            $query = self::getLatestRankingsQuery();
            
            $resultset->setBaseQuery("
                {$query}
                {{WHERE_CRITERIA}}
            ");
            
            //Set default sort criteria
            $resultset->setSortCriteria('dre.rank', 'ASC');
        }
        else {
            $resultset = new Redis(CacheEntryNames::DAILY_RANKINGS);
        }
        
        $resultset->enableTotalRecordCount();
        
        //Set default rows per page
        $resultset->setRowsPerPage($rows_per_page);    
        
        //Set the default page number
        $resultset->setPageNumber($page_number);  
        
        return $resultset;
    }
    
    public static function getRankingsFromCache($page_number = 1, $rows_per_page, $number_of_days = NULL) {        
        $rankings_name = CacheEntryNames::DAILY_RANKINGS;
        $ranking_entries_name = CacheEntryNames::DAILY_RANKING_ENTRIES;
        $ranking_entries_filter_name = CacheEntryNames::DAILY_RANKING_ENTRIES_FILTER;
        
        if(!empty($number_of_days)) {
            $rankings_name = CacheEntryNames::generateDailyDayTypeRankingName($number_of_days);
            $ranking_entries_name = CacheEntryNames::generateDailyDayTypeRankingEntriesName($number_of_days);
            $ranking_entries_filter_name = CacheEntryNames::generateDailyDayTypeRankingEntriesFilterName($number_of_days);
        }
        
        $resultset = new Redis($rankings_name, cache('read'));
        
        $resultset->setEntriesName($ranking_entries_name);
        $resultset->setFilterName($ranking_entries_filter_name);
        
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
    
    public static function deleteRankingsInCache() {
        $cache = cache('write');
    
        $cache->reval(file_get_contents(Necrolab::getLuaScriptPath() . '/delete_daily_ranking_season.lua'), array(
            CacheEntryNames::DAILY_RANKINGS,
            CacheEntryNames::DAILY_RANKING_ENTRIES,
            CacheEntryNames::DAILY_RANKING_ENTRIES_FILTER
        ));
        
        $cache->reval(file_get_contents(Necrolab::getLuaScriptPath() . '/delete_daily_ranking_season.lua'), array(
            CacheEntryNames::DAILY_RANKINGS_30_DAY,
            CacheEntryNames::DAILY_RANKING_ENTRIES_30_DAY,
            CacheEntryNames::DAILY_RANKING_ENTRIES_FILTER_30_DAY
        ));
        
        $cache->reval(file_get_contents(Necrolab::getLuaScriptPath() . '/delete_daily_ranking_season.lua'), array(
            CacheEntryNames::DAILY_RANKINGS_100_DAY,
            CacheEntryNames::DAILY_RANKING_ENTRIES_100_DAY,
            CacheEntryNames::DAILY_RANKING_ENTRIES_FILTER_100_DAY
        ));
    }
    
    public static function loadRankingsIntoCache() {
        $cache = cache('write');
        
        $query = self::getLatestRankingsQuery();

        $latest_daily_rankings = db()->prepareExecuteQuery("
            {$query}
            ORDER BY dre.rank ASC
        ");

        $transaction = $cache->transaction();

        while($latest_daily_ranking = $latest_daily_rankings->fetch(PDO::FETCH_ASSOC)) {
            $steamid = $latest_daily_ranking['steamid'];
            $personaname = $latest_daily_ranking['personaname'];
            
            $entry_hash_name = CacheEntryNames::generateDailyRankingEntryName($steamid);
            
            $transaction->hMset($entry_hash_name, $latest_daily_ranking);
            $transaction->zAdd(CacheEntryNames::DAILY_RANKING_ENTRIES, $latest_daily_ranking['rank'], $entry_hash_name);
            
            if(!empty($personaname)) {
                $transaction->hSet(CacheEntryNames::DAILY_RANKING_ENTRIES_FILTER, $personaname, $entry_hash_name);
            }
        }

        $transaction->set(CacheEntryNames::DAILY_RANKINGS_LAST_UPDATED, date('Y-m-d H:i:s'));
        
        $transaction->commit();
    }
    
    public static function loadDailyLeaderboardsIntoCache($cache = NULL) {    
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $current_date = new DateTime();
        
        $daily_leaderboards = db()->prepareExecuteQuery("
            SELECT
                l.lbid,
                l.name,
                c.name AS character_name,
                l.is_speedrun,
                l.is_custom,
                l.is_co_op,
                l.is_seeded,
                l.is_daily,
                l.daily_date,
                l.is_score_run,
                l.is_all_character,
                l.is_deathless,
                l.is_story_mode,
                l.is_dev,
                l.is_prod,
                l.is_power_ranking,
                l.is_daily_ranking
            FROM leaderboards l
            JOIN characters c ON c.character_id = l.character_id
            WHERE l.is_daily_ranking = 1
            ORDER BY l.daily_date ASC
        ");

        $transaction = $cache->transaction();

        while($daily_leaderboard = $daily_leaderboards->fetch(PDO::FETCH_ASSOC)) {
            $lbid = $daily_leaderboard['lbid'];
        
            $leaderboard_name = CacheEntryNames::generateLeaderboardName($lbid);
            
            $transaction->hSet(CacheEntryNames::LEADERBOARDS, $lbid, $leaderboard_name);
            $transaction->hMSet($leaderboard_name, $daily_leaderboard);
            $transaction->hSet(CacheEntryNames::DAILY_LEADERBOARDS, $lbid, $leaderboard_name);
            $transaction->hSet(CacheEntryNames::DAILY_LEADERBOARDS_BY_DATE, $daily_leaderboard['daily_date'], $leaderboard_name);
            
            $daily_ranking_day_types = static::getDayTypesFromDatabase();
            
            if(!empty($daily_ranking_day_types)) {
                foreach($daily_ranking_day_types as $daily_ranking_day_type) {
                    $number_of_days = $daily_ranking_day_type['number_of_days'];
                
                    $daily_date = $daily_leaderboard['daily_date'];
                    $daily_date_object = new DateTime($daily_date);
                    
                    $daily_date_difference = $daily_date_object->diff($current_date);
                    
                    if($daily_date_difference->format('%a') <= $number_of_days) {
                        $transaction->hSet(CacheEntryNames::generateDailyDayTypeLeaderboardsName($number_of_days), $daily_date, $leaderboard_name);
                    }
                }
            }
        }
        
        $transaction->commit();
    }
    
    public static function loadDailyRankingDayTypesIntoCache($cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
    
        $daily_day_types = db()->getMappedColumn("
            SELECT
                number_of_days,
                daily_ranking_day_type_id
            FROM daily_ranking_day_types
            WHERE enabled = 1
        ");
        
        $cache->hMSet(CacheEntryNames::DAILY_RANKING_DAY_TYPES, $daily_day_types);
    }
    
    public static function loadDailyLeaderboardEntriesIntoCache($cache = NULL) {    
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $transaction = $cache->transaction();

        $daily_entries = db()->prepareExecuteQuery("
            SELECT 
                l.lbid,
                l.daily_date, 
                c.name AS character_name,     
                le.rank,
                le.details,
                le.score,
                le.is_win,
                su.steamid,
                su.personaname
            FROM leaderboards l
            JOIN leaderboard_snapshots ls ON ls.leaderboard_snapshot_id = l.last_snapshot_id
            JOIN leaderboard_entries le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            JOIN characters c ON c.character_id = l.character_id
            JOIN steam_users su ON su.steam_user_id = le.steam_user_id
            WHERE l.is_daily_ranking = 1
            ORDER BY l.daily_date, le.rank
        ");

        while($daily_entry = $daily_entries->fetch(PDO::FETCH_ASSOC)) {
            $lbid = $daily_entry['lbid'];
            $daily_date = $daily_entry['daily_date'];
            $character_name = $daily_entry['character_name'];
            $steamid = $daily_entry['steamid'];
            $rank = $daily_entry['rank'];
            $score = $daily_entry['score'];
            $personaname = $daily_entry['personaname'];

            $leaderboard_hash_name = CacheEntryNames::generateLeaderboardName($lbid);
            $entries_hash_name = CacheEntryNames::generateLeaderboardEntriesName($lbid);
            $entry_hash_name = CacheEntryNames::generateLeaderboardEntryName($lbid, $steamid);
            
            $transaction->hMSet($entry_hash_name, array(
                'lbid' => (int)$lbid,
                'steamid' => $steamid,
                'rank' => (int)$rank,
                'details' => $daily_entry['details'],
                'score' => (int)$score,
                'is_win' => (int)$daily_entry['is_win']
            ));
                
            $transaction->zAdd($entries_hash_name, $rank, $entry_hash_name);
            
            $transaction->hIncrBy(CacheEntryNames::DAILY_LEADERBOARDS_SCORE_TOTALS, $leaderboard_hash_name, $score);
            
            if(!empty($personaname)) {
                $transaction->hSet(CacheEntryNames::generateLeaderboardEntriesFilterName($lbid), $personaname, $entry_hash_name);
            }
        }

        $transaction->commit();
    }
}