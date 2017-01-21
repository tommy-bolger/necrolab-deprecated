<?php

namespace Modules\Necrolab\Models\SteamUsers\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;
use \Modules\Necrolab\Models\Leaderboards\Cache\CacheNames as LeaderboardCacheNames;
use \Modules\Necrolab\Models\DailyRankings\Cache\CacheNames as DailyRankingCacheNames;
use \Modules\Necrolab\Models\DailySeasons\Cache\CacheNames as DailySeasonCacheNames;

class CacheNames {    
    const STEAM_USERS = 'su';
    
    const STEAM_USERS_BY_NAME = 'n';
    
    const LAST_UPDATED = 'lu';
    
    const UPDATE_JSON = 'uj';
    
    public static function getBaseName() {
        return self::STEAM_USERS;
    }
    
    public static function getUsersByName() {
        return self::getBaseName() . ':' . self::STEAM_USERS_BY_NAME;
    }
    
    public static function getLastUpdatedName() {
        return self::getBaseName() . ':' . self::LAST_UPDATED;
    }
    
    public static function getSteamUserEntryName($steamid) {
        return self::getBaseName() . ":{$steamid}";
    }
    
    public static function getUpdateRecordName() {
        return self::getBaseName() . ':' . self::UPDATE_RECORDS;
    }
    
    public static function getUpdateRecordEntriesName() {
        return self::getUpdateRecordName() . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getUpdateRecordEntryName($steamid) {
        return self::getUpdateRecordEntriesName() . ":{$steamid}";
    }

    public static function getSpeedrunLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . LeaderboardCacheNames::getSpeedrunLeaderboardsName();
    }
    
    public static function getScoreLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . LeaderboardCacheNames::getScoreLeaderboardsName();
    }
    
    public static function getCustomLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . LeaderboardCacheNames::getCustomLeaderboardsName();
    }
    
    public static function getCoOpLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . LeaderboardCacheNames::getCoOpLeaderboardsName();
    }
    
    public static function getSeededLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . LeaderboardCacheNames::getSeededLeaderboardsName();
    }
    
    public static function getDeathlessLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . LeaderboardCacheNames::getDeathlessLeaderboardsName();
    }
    
    public static function getCharacterLeaderboardsName($steamid, $character_name) {
        return self::getSteamUserEntryName($steamid) . ':' . LeaderboardCacheNames::getCharacterLeaderboardsName($character_name);
    }
    
    public static function getDailyLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . DailyRankingCacheNames::getLeaderboardsName();
    }
    
    public static function getDailyByDateLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . DailyRankingCacheNames::getLeaderboardsByDateName();
    }
    
    public static function getDailySeasonLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . DailySeasonCacheNames::getLeaderboardsName();
    }
    
    public static function getDailySeasonByDateLeaderboardsName($season_number, $steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . DailySeasonCacheNames::getLeaderboardsName($season_number);
    }
    
    public static function getPowerLeaderboardsName($steamid) {
        return self::getSteamUserEntryName($steamid) . ':' . LeaderboardCacheNames::getPowerLeaderboardsName();
    }
    
    public static function getUpdateJsonName() {
        return self::getBaseName() . ':' . self::UPDATE_JSON;
    }
}