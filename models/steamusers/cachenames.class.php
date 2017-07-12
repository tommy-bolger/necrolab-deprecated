<?php

namespace Modules\Necrolab\Models\SteamUsers;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;

class CacheNames
extends BaseCacheNames {    
    const STEAM_USERS = 'steam_users';
    
    const ALL_RECORDS = 'records';
    
    const STEAM_USERS_BY_NAME = 'names';
    
    const PBS = 'pbs';

    public static function getBaseName() {
        return self::STEAM_USERS;
    }
    
    public static function getIdsName() {
        return self::getBaseName() . ':'  . BaseCacheNames::IDS;
    }
    
    public static function getAllRecordsName() {
        return self::getBaseName() . ':'  . self::ALL_RECORDS;
    }
    
    public static function getUsersByName() {
        return self::getBaseName() . ':' . self::STEAM_USERS_BY_NAME;
    }
    
    public static function getUsersIndexName(array $index_segments = array()) {                
        return parent::getIndexName(self::getBaseName() . ':' . BaseCacheNames::INDEX, $index_segments);
    }
    
    public static function getAllPbsName() {
        return self::getBaseName() . ':'  . self::PBS;
    }
    
    public static function getPbsIndexName(array $index_segments = array()) {
        return parent::getIndexName(self::getAllPbsName() . ':' . BaseCacheNames::INDEX, $index_segments);
    }
}