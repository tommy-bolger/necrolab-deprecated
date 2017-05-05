<?php

namespace Modules\Necrolab\Models\SteamUsers\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;

class CacheNames {    
    const STEAM_USERS = 'steam_users';
    
    const IDS = 'ids';
    
    const ALL_RECORDS = 'records';
    
    const STEAM_USERS_BY_NAME = 'names';

    public static function getBaseName() {
        return self::STEAM_USERS;
    }
    
    public static function getIdsName() {
        return self::getBaseName() . ':'  . self::IDS;
    }
    
    public static function getAllRecordsName() {
        return self::getBaseName() . ':'  . self::ALL_RECORDS;
    }
    
    public static function getUsersByName() {
        return self::getBaseName() . ':' . self::STEAM_USERS_BY_NAME;
    }
}