<?php
namespace Modules\Necrolab\Models\Leaderboards\Cache;

use \Exception;
use \Modules\Necrolab\Models\Leaderboards\Replays as BaseReplays;
use \Modules\Necrolab\Models\Leaderboards\Database\Replays as DatabaseReplays;

class Replays
extends BaseReplays {
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

    public static function encodeRecord(array $replay) {
        return gzencode(json_encode($replay, JSON_UNESCAPED_UNICODE), 9);
    }
    
    public static function decodeRecord($encoded_replay) {
        return json_decode(gzdecode($encoded_replay));
    }
    
    public static function populate() {
        $resultset = DatabaseReplays::getAllResultset();
        
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
