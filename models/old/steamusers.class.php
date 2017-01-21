<?php
namespace Modules\Necrolab\Models;

use \PDO;
use \Exception;
use \Framework\Core\Framework;
use \Framework\Core\Loader;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;
use \Framework\Modules\Module;
use \Framework\Utilities\ParallelProcessQueue;
use \Modules\Necrolab\Objects\CacheEntryNames;
use \Modules\Necrolab\Objects\SteamUser;
use \HTMLPurifier_Config;
use \HTMLPurifier;

class SteamUsers {
    protected static function getUserFromDatabase($steamid) {
        return db()->getRow("
            SELECT *
            FROM steam_users
            WHERE steamid = ?
        ", array(
            $steamid
        ));
    }

    public static function getUserFromCache($steamid, $transaction = NULL) {
        $steam_user = array();
        
        $steam_record_name = CacheEntryNames::generateSteamUserEntryName($steamid);
    
        if(empty($transaction)) {
            $steam_user = cache('read')->hGetAll($steam_record_name);
        }
        else {
            $transaction->hGetAll($steam_record_name);
        }
        
        return $steam_user;
    }

    public static function getLatestPowerRankingFromDatabase($steamid) {
        return db()->getRow("
            SELECT pre.*
            FROM steam_users su
            JOIN power_ranking_entries pre ON pre.power_ranking_entry_id = su.latest_power_ranking_entry_id 
            WHERE su.steamid = ?   
        ", array(
            $steamid
        ));
    }
    
    public static function getLatestPowerRankingFromCache($steamid, $transaction = NULL) {    
        $power_ranking_entry = array();
        
        $power_ranking_entry_name = CacheEntryNames::generatePowerRankingEntryName($steamid);
    
        if(empty($transaction)) {
            $power_ranking_entry = cache('read')->hGetAll($power_ranking_entry_name);
        }
        else {
            $transaction->hGetAll($power_ranking_entry_name);
        }
        
        return $power_ranking_entry;
    }
    
    public static function getLatestDailyRankingsFromDatabase($steamid, $number_of_days = NULL, $transaction = NULL) {        
        return db()->getAll("
            SELECT
                drdt.daily_ranking_day_type_id,
                drdt.number_of_days,
                su.steamid,
                dre.rank,
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
                dre.daily_ranking_entry_id,
                dre.steam_user_id
            FROM steam_users su
            JOIN steam_users_latest_daily_rankings suldr ON suldr.steam_user_id = su.steam_user_id
            JOIN daily_rankings dr ON dr.daily_ranking_id = suldr.daily_ranking_id
            JOIN daily_ranking_entries dre ON dre.daily_ranking_id = suldr.daily_ranking_entry_id
            LEFT JOIN daily_ranking_day_types drdt ON drdt.daily_ranking_day_type_id = dr.daily_ranking_day_type_id
            WHERE su.steamid = ?   
        ", array(
            $steamid
        ));
    }
    
    public static function getLatestDailyRankingsFromCache($steamid) {
        $day_types = DailyRankings::getDayTypesFromCache();
        
        $user_daily_rankings = array();
        
        if(!empty($day_types)) {
            $day_type_names = array();
            $day_type_hash_names = array();
        
            foreach($day_types as $number_of_days => $daily_ranking_day_type_id) {
                $day_type_names[] = $number_of_days;
                $day_type_hash_names[] = CacheEntryNames::generateDailyDayTypeRankingEntryName($number_of_days, $steamid);
            }
            
            $day_type_hash_names[] = CacheEntryNames::generateDailyRankingEntryName($steamid);
            $day_type_names[] = 'all_time';
            
            $daily_rankings_from_cache = cache('read')->hGetAllMulti($day_type_hash_names);
            
            if(!empty($daily_rankings_from_cache)) {
                foreach($daily_rankings_from_cache as $index => $daily_ranking_from_cache) {
                    if(!empty($daily_ranking_from_cache)) {
                        $user_daily_rankings[$day_type_names[$index]] = $daily_ranking_from_cache;
                    }
                }
            }
        }
        
        return $user_daily_rankings;
    }
    
    public static function getLatestDailyRankingSeasonEntryFromCache($steamid, $transaction = NULL) {
        $season_entry_hash_name = CacheEntryNames::generateDailyRankingSeasonEntryName($steamid);
        
        $season_ranking_record = array();
        
        if(!empty($transaction)) {
            $transaction->hGetAll($season_entry_hash_name);
        }
        else {
            $season_ranking_record = cache('read')->hGetAll($season_entry_hash_name);
        }
        
        return $season_ranking_record;
    }
    
    public static function getUserSpeedLeaderboards($steamid, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserSpeedrunLeaderboardName($steamid);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserScoreLeaderboards($steamid, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserScoreLeaderboardName($steamid);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserCustomLeaderboards($steamid, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserCustomLeaderboardName($steamid);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserCoOpLeaderboards($steamid, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserCoOpLeaderboardName($steamid);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserSeededLeaderboards($steamid, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserSeededLeaderboardName($steamid);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserDeathlessLeaderboards($steamid, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserDeathlessLeaderboardName($steamid);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserCharacterLeaderboards($steamid, $character_name, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserCharacterLeaderboardName($steamid, $character_name);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserDailyByDateLeaderboards($steamid, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserDailyByDateLeaderboardName($steamid);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserDailySeasonByDateLeaderboards($steamid, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserDailySeasonByDateLeaderboardName($steamid);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserPowerLeaderboards($steamid, $transaction = NULL) {
        $leaderboards_record_name = CacheEntryNames::generateSteamUserPowerLeaderboardName($steamid);
        
        $user_leaderboards = array();
        
        if(empty($transaction)) {
            $cache = cache('read');
            
            $entry_names = $cache->hGetAll($leaderboards_record_name);
            
            $user_leaderboards = $cache->hGetAllMulti($entry_names);
        }
        else {
            $transaction->hGetAll($leaderboards_record_name);
        }
        
        return $user_leaderboards;
    }
    
    public static function getUserCategoryLeaderboards($steamid, $category_name, array $leaderboard_entries) {
        assert('!empty($leaderboard_entries)');
    
        $empty_leaderboard_row = array(
            'name' => '',
            'cadence' => NULL,
            'bard' => NULL,
            'monk' => NULL,
            'aria' => NULL,
            'bolt' => NULL,
            'dove' => NULL,
            'eli' => NULL,
            'melody' => NULL,
            'dorian' => NULL,
            'coda' => NULL,
            'all' => NULL,
            'story' => NULL
        );
    
        $ungrouped_leaderboards = Leaderboards::getAllByCategoryFromCache($category_name);
            
        $grouped_leaderboards = Leaderboards::getGroupedLeaderboards($category_name, $ungrouped_leaderboards);
    
        $category_entries = array();
    
        if(!empty($grouped_leaderboards)) {
            foreach($grouped_leaderboards as $leaderboard_group_name => $leaderboard_group) {
                $leaderboard_characters = $leaderboard_group['characters'];
                
                $rank_row = array();
            
                foreach($leaderboard_characters as $character_name => $lbid) {
                    foreach($leaderboard_entries as $leaderboard_entry) {
                        if($leaderboard_entry['lbid'] == $lbid) {
                            $rank_row['name'] = $leaderboard_group['name'];
                            $rank_row[$character_name] = $leaderboard_entry['rank'];
                        }
                    }
                }
                
                if(!empty($rank_row)) {
                    $group_rank_row = array_merge($empty_leaderboard_row, $rank_row);
                    
                    $category_entries[] = $group_rank_row;
                }
            }
        }
        
        return $category_entries;
    }
    
    public static function saveSocialMediaData($steamid, $twitch_username, $nico_nico_url, $hitbox_username, $twitter_username, $website) {
        Loader::load('HTMLPurifier/HTMLPurifier.auto.php');
        
        $steam_user = new SteamUser($steamid);
        
        $purifier_configuration = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($purifier_configuration);
        
        $steam_user->twitch_username = $purifier->purify($twitch_username);
        $steam_user->nico_nico_url = $purifier->purify($nico_nico_url);
        $steam_user->hitbox_username = $purifier->purify($hitbox_username);
        $steam_user->twitter_username = $purifier->purify($twitter_username);
        $steam_user->website = $purifier->purify($website);
    
        $social_media_data = array(
            'twitch_username' => $twitch_username,
            'nico_nico_url' => $nico_nico_url,
            'hitbox_username' => $hitbox_username,
            'twitter_username' => $twitter_username,
            'website' => $website
        );
        
        $transaction = cache('write')->transaction();
    
        self::saveUserToCache($steam_user->toCacheArray(), $transaction, $steam_user->toArray(false));
        
        $transaction->commit();
    }
    
    public static function loadUsersToCache() {
        $steam_users = db()->prepareExecuteQuery("
            SELECT 
                steamid,
                updated,
                nico_nico_url,
                hitbox_username,
                personaname,
                twitch_username,
                twitter_username,
                website
            FROM steam_users
        ");
        
        $transaction = cache('write')->transaction();
        
        while($steam_user = $steam_users->fetch(PDO::FETCH_ASSOC)) {
            $steam_user_record = new SteamUser($steam_user['steamid']);
            $steam_user_record->setPropertiesFromArray($steam_user);
            
            self::saveUserToCache($steam_user_record->toCacheArray(), $transaction);
        }
        
        $transaction->commit();
    }
    
    public static function saveUserToCache(array $user_record, $transaction, array $database_record = array()) {
        if(empty($user_record['steamid'])) {
            throw new Exception("steamid is required to save this record.");
        }
        
        $steamid = $user_record['steamid'];
        $personaname = NULL;
        
        if(!empty($user_record['personaname'])) {
            $personaname = $user_record['personaname'];
        }
        
        $updated = NULL;
            
        if(!empty($user_record['updated'])) {
            $updated = strtotime($user_record['updated']);
        }
        else {
            $updated = time();
        }
        
        unset($user_record['updated']);
        
        $hash_name = CacheEntryNames::generateSteamUserEntryName($steamid);
        
        $transaction->hMSet($hash_name, $user_record);
        
        $transaction->hSet(CacheEntryNames::STEAM_USERS, $steamid, $hash_name);
        $transaction->zAdd(CacheEntryNames::STEAM_USERS_LAST_UPDATED, $updated, $steamid);

        if(!empty($personaname)) {
            $transaction->hSet(CacheEntryNames::STEAM_USERS_BY_NAME, $steamid, $personaname);
        }
    
        if(!empty($database_record)) {
            //Queue this user to be added to the database
            $update_record_name = CacheEntryNames::generateSteamUserUpdateRecordEntryName($steamid);
            
            $transaction->hMSet($update_record_name, $database_record);
            
            $transaction->hSet(CacheEntryNames::STEAM_USERS_UPDATE_RECORD_ENTRIES, $steamid, $update_record_name);
        }
    }

    public static function updateUserGroupFromSteam(array $request_steam_ids, $steam_api_key) {        
        $request_context = stream_context_create(array(
            'http' => array(
                'timeout' => 180
            )
        ));
      
        $request_steam_ids_string = implode(',', $request_steam_ids);
        
        $steam_users_json = file_get_contents(
            "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$steam_api_key}&steamids={$request_steam_ids_string}", 
            false,
            $request_context
        );
        
        $steam_users_data = json_decode($steam_users_json);

        if(!empty($steam_users_data->response->players)) {
            $transaction = cache('write', true)->transaction();
        
            foreach($steam_users_data->response->players as $steam_user_data) { 
                $steam_user = new SteamUser($steam_user_data->steamid);
                $steam_user->setPropertiesFromObject($steam_user_data);
                $steam_user->updated = date('Y-m-d H:i:s');    
                
                self::saveUserToCache($steam_user->toCacheArray(), $transaction, $steam_user->toArray(false));
            }
            
            $transaction->commit();
        }
    }
    
    public static function updateUsersFromSteam() {
        $users_to_update = cache('read')->zRevRangeByScore(CacheEntryNames::STEAM_USERS_LAST_UPDATED, strtotime('-1 day'), '-inf', array(
            'withscores' => true
        ));
        
        if(!empty($users_to_update)) {
            $module = new Module('necrolab');
            
            $steam_api_key = $module->configuration->steam_api_key;
        
            $steam_update_job_queue = new ParallelProcessQueue();
            
            $steam_update_job_queue->setMaxParallelProcesses(10);
        
            $steamids_group = array();
            $group_counter = 1;
        
            foreach($users_to_update as $steamid => $time_last_updated) {
                if($group_counter == 101) {                    
                    $steam_update_job_queue->addProcessToQueue(array(get_called_class(), 'updateUserGroupFromSteam'), array(
                        'steamids_group' => $steamids_group,
                        $steam_api_key
                    ));
                    
                    $group_counter = 1;
                    $steamids_group = array();
                }
                
                $steamids_group[] = $steamid;
            
                $group_counter += 1;
            }
            
            if(!empty($steamids_group)) {
                $steam_update_job_queue->addProcessToQueue(array(get_called_class(), 'updateUserGroupFromSteam'), array(
                    'steamids_group' => $steamids_group,
                    $steam_api_key
                ));
            }
            
            $steam_update_job_queue->run();
        }
    }
    
    public static function insertUpdateUserRecords($user_update_records) {
        if(!empty($user_update_records)) {            
            foreach($user_update_records as $user_update_record) {
                $steamid = $user_update_record['steamid'];
            
                $steam_user_id = db()->getOne("
                    SELECT steam_user_id
                    FROM steam_users
                    WHERE steamid = :steamid
                ", array(
                    $steamid
                ));
                
                if(empty($steam_user_id)) {
                    db()->insert('steam_users', $user_update_record);
                }
                else {
                    db()->update('steam_users', $user_update_record, array(
                        'steam_user_id' => $steam_user_id
                    ));
                }
            }
        }
    }
    
    public static function saveUserUpdatesFromCache() {
        $write_cache = cache('write');
        $read_cache = cache('read');
        
        $user_update_entries_hash_name = CacheEntryNames::STEAM_USERS_UPDATE_RECORD_ENTRIES;
        
        $user_change_records = $read_cache->hGetAll($user_update_entries_hash_name);
        
        if(!empty($user_change_records)) {
            //First loop and transaction to add all update records to the database
            $read_transaction = $write_cache->transaction();
            
            $read_transaction->setCommitProcessCallback(array(get_called_class(), 'insertUpdateUserRecords'));  
        
            foreach($user_change_records as $steamid => $user_change_record) {
                $read_transaction->hGetAll($user_change_record);
            }
            
            $read_transaction->commit();
            
            //Second loop and transaction to remove the update records from cache
            $write_transaction = $write_cache->transaction();
            
             foreach($user_change_records as $steamid => $user_change_record) {
                $write_transaction->hDel($user_update_entries_hash_name, $steamid);
                $write_transaction->del($user_change_record);
            }
            
            $write_transaction->commit();
        }
    }
}
