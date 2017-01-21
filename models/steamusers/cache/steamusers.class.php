<?php
namespace Modules\Necrolab\Models\SteamUsers\Cache;

use \PDO;
use \Exception;
use \Framework\Core\Loader;
use \Framework\Data\ResultSet\Redis;
use \Framework\Modules\Module;
use \Framework\Utilities\ParallelProcessQueue;
use \HTMLPurifier_Config;
use \HTMLPurifier;
use \Modules\Necrolab\Models\SteamUsers\SteamUsers as BaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Cache\RecordModels\SteamUser;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;
use \Modules\Necrolab\Models\Leaderboards\Cache\Leaderboards;
use \Modules\Necrolab\Models\DailyRankings\Cache\DailyRankings;
use \Modules\Necrolab\Models\DailyRankings\Cache\CacheNames as DailyRankingCacheNames;
use \Modules\Necrolab\Models\Rankings\Cache\Power\CacheNames as PowerCacheNames;
use \Modules\Necrolab\Models\DailySeasons\Cache\DailySeasons;
use \Modules\Necrolab\Models\DailySeasons\Cache\CacheNames as DailySeasonCacheNames;

class SteamUsers
extends BaseSteamUsers {
    public static function load($steamid) {
        if(empty(static::$users[$steamid])) {
            static::$users[$steamid] = static::getUserRecord($steamid);
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            static::$user_ids = cache('read')->hGetAll(CacheNames::getBaseName());
        }
    }

    public static function getUserRecord($steamid, $transaction = NULL) {
        $steam_user = array();
        
        $steam_record_name = CacheNames::getSteamUserEntryName($steamid);
    
        if(empty($transaction)) {
            $steam_user = cache('read')->hGetAll($steam_record_name);
        }
        else {
            $transaction->hGetAll($steam_record_name);
        }
        
        return $steam_user;
    }
    
    public static function getRankingsResultset() {
        $cache = cache('read');
    
        //TODO: Finish this
        $resultset = new Redis(CacheNames::getLeaderboardName($lbid), $cache);
        
        $resultset->setEntriesName(CacheNames::getEntriesName($lbid));
        
        $resultset->setEntryNameCallback(array(
            CacheNames::getFullClassName(),
            'getEntryName'
        ), array($lbid));
        
        $resultset->setRowsPerPage(100);
        
        return $resultset;
    }
    
    public static function getLatestPowerRanking($steamid, $transaction = NULL) {    
        $power_ranking_entry = array();
        
        $power_ranking_entry_name = PowerCacheNames::getPowerRankingEntryName($steamid);
    
        if(empty($transaction)) {
            $power_ranking_entry = cache('read')->hGetAll($power_ranking_entry_name);
        }
        else {
            $transaction->hGetAll($power_ranking_entry_name);
        }
        
        return $power_ranking_entry;
    }
    
    public static function getLatestDailyRankings($steamid) {
        $day_types = DailyRankings::getDayTypesFromCache();
        
        $user_daily_rankings = array();
        
        if(!empty($day_types)) {
            $day_type_names = array();
            $day_type_hash_names = array();
        
            foreach($day_types as $number_of_days => $daily_ranking_day_type_id) {
                $day_type_names[] = $number_of_days;
                $day_type_hash_names[] = DailyRankingCacheNames::getEntryName($number_of_days, $steamid);
            }
            
            $day_type_hash_names[] = DailyRankingCacheNames::getEntryName($number_of_days, $steamid);
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
    
    public static function getLatestDailySeasonEntry($season_number, $steamid, $transaction = NULL) {    
        $season_entry_hash_name = DailySeasonCacheNames::getRankingEntryName($season_number, $steamid);
        
        $season_ranking_record = array();
        
        if(!empty($transaction)) {
            $transaction->hGetAll($season_entry_hash_name);
        }
        else {
            $season_ranking_record = cache('read')->hGetAll($season_entry_hash_name);
        }
        
        return $season_ranking_record;
    }
    
    protected static function getUserLeaderboards($leaderboards_record_name, $transaction = NULL) {
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
    
    public static function getSpeedLeaderboards($steamid, $transaction = NULL) {
        return static::getUserLeaderboards(CacheNames::getSpeedrunLeaderboardsName($steamid), $transaction);
    }
    
    public static function getScoreLeaderboards($steamid, $transaction = NULL) {        
        return static::getUserLeaderboards(CacheNames::getScoreLeaderboardsName($steamid), $transaction);
    }
    
    public static function getCustomLeaderboards($steamid, $transaction = NULL) {        
        return static::getUserLeaderboards(CacheNames::getCustomLeaderboardsName($steamid), $transaction);
    }
    
    public static function getCoOpLeaderboards($steamid, $transaction = NULL) {
        return static::getUserLeaderboards(CacheNames::getCoOpLeaderboardsName($steamid), $transaction);
    }
    
    public static function getSeededLeaderboards($steamid, $transaction = NULL) {        
        return static::getUserLeaderboards(CacheNames::getSeededLeaderboardsName($steamid), $transaction);
    }
    
    public static function getDeathlessLeaderboards($steamid, $transaction = NULL) {        
        return static::getUserLeaderboards(CacheNames::getDeathlessLeaderboardsName($steamid), $transaction);
    }
    
    public static function getCharacterLeaderboards($steamid, $character_name, $transaction = NULL) {        
        return static::getUserLeaderboards(CacheNames::getCharacterLeaderboardsName($steamid, $character_name), $transaction);
    }
    
    public static function getDailyByDateLeaderboards($steamid, $transaction = NULL) {        
        return static::getUserLeaderboards(CacheNames::getDailyByDateLeaderboardsName($steamid), $transaction);
    }
    
    public static function getDailySeasonByDateLeaderboards($season_number, $steamid, $transaction = NULL) {        
        return static::getUserLeaderboards(CacheNames::getDailySeasonByDateLeaderboardsName($season_number, $steamid), $transaction);
    }
    
    public static function getUserPowerLeaderboards($steamid, $transaction = NULL) {        
        return static::getUserLeaderboards(CacheNames::getPowerLeaderboardsName($steamid), $transaction);
    }
    
    public static function getSocialMediaData(array $steamids) {
        $steam_user_record_names = array();
        
        if(!empty($steamids)) {
            foreach($steamids as $steamid) {
                $steam_user_record_names[] = CacheNames::getSteamUserEntryName($steamid);
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
    
    protected static function getUngroupedLeaderboards($category_name) {
        return Leaderboards::getAllByCategory($category_name);
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
        
        $transaction = cache('write')->transaction();
    
        static::saveUser($steam_user->toArray(false), $transaction, $steam_user->toArray(false));
        
        $transaction->commit();
    }
    
    public static function save(array $user_record, $transaction, array $database_record = array()) {    
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
            
            unset($user_record['updated']);
        }
        else {
            $updated = time();
        }
        
        $hash_name = CacheNames::getSteamUserEntryName($steamid);
        
        $transaction->hMSet($hash_name, $user_record);
        
        $transaction->sAdd(CacheNames::STEAM_USERS, $steamid);
        $transaction->zAdd(CacheNames::getLastUpdatedName(), $updated, $steamid);

        if(!empty($personaname)) {
            $transaction->hSet(CacheNames::getUsersByName(), $steamid, $personaname);
        }
    
        if(!empty($database_record)) {
            //Queue this user to be added to the database
            $update_record_name = CacheNames::getUpdateRecordName($steamid);
            
            $transaction->hMSet($update_record_name, $database_record);
            
            $transaction->hSet(CacheNames::getUpdateRecordEntriesName(), $steamid, $update_record_name);
        }
    }
    
    public function actionLoadIntoCache() {
        $steam_users_resultset = DatabaseSteamUsers::getAllUsers();
    
        $steam_users = $steam_users_resultset->prepareExecuteQuery();
        
        $transaction = cache('write')->transaction();
        
        while($steam_user = $steam_users->fetch(PDO::FETCH_ASSOC)) {          
            $steam_user_record = new SteamUser();
            $steam_user_record->setPropertiesFromArray($steam_user);
            
            static::saveUser($steam_user_record->toArray(false), $transaction);
        }
        
        $transaction->commit();
    }
}
