<?php
namespace Modules\Necrolab\Models\SteamUsers\Cache;

use \Exception;
use \Framework\Data\ResultSet\Redis;
use \Modules\Necrolab\Models\SteamUsers\SteamUsers as BaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as DatabaseSteamUsers;

class SteamUsers
extends BaseSteamUsers {
    public static function load($steamid) {
        if(empty(static::$users[$steamid])) {
            static::$users[$steamid] = cache('read')->hGet(CacheNames::getAllRecordsName(), $steamid);
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            static::$user_ids = cache('read')->hGetAll(CacheNames::getIdsName());
        }
    }

    public static function encodeRecord(array $steam_user) {
        return gzencode(json_encode($steam_user, JSON_UNESCAPED_UNICODE), 9);
    }
    
    public static function decodeRecord($encoded_steam_user) {
        return json_decode(gzdecode($encoded_steam_user));
    }
    
    public static function populate() {
        $resultset = DatabaseSteamUsers::getAllResultset();
        
        $resultset->setAsCursor(100000);
        
        db()->beginTransaction();
        
        $resultset->prepareExecuteQuery();
        
        $steam_users_cache_name = CacheNames::getAllRecordsName();
        $steam_users_names_cache_name = CacheNames::getUsersByName();
        
        $transaction = cache()->transaction();
        
        $steam_users = array();
        
        do {
            $steam_users = $resultset->getNextCursorChunk();
        
            if(!empty($steam_users)) {
                foreach($steam_users as $steam_user) {         
                    $steam_user_id = $steam_user['steam_user_id'];
                    $personaname = $steam_user['personaname'];
                
                    $transaction->hSet($steam_users_cache_name, $steam_user_id, static::encodeRecord($steam_user));
                    
                    $transaction->hSet($steam_users_names_cache_name, $steam_user_id, $personaname);
                }
            }
        }
        while(!empty($steam_users));
        
        $transaction->commit();
        
        db()->commit();
    }
    
    public static function getUsersFromIds(array $steam_user_ids) {
        $steam_users = array();
    
        if(!empty($steam_user_ids)) {
            $steam_user_records = cache('read')->hMGet(CacheNames::getAllRecordsName(), $steam_user_ids);
            
            if(!empty($steam_user_records)) {
                foreach($steam_user_records as $steam_user_id => $steam_user_record) {
                    $steam_users[$steam_user_id] = static::decodeRecord($steam_user_record);
                }
            }
        }
        
        return $steam_users;
    }
}
