<?php

namespace Modules\Necrolab\Models\Leaderboards\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;
use \Modules\Necrolab\Models\Characters\Cache\CacheNames as CharacterCacheNames;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames as RankingCacheNames;

class CacheNames
extends BaseCacheNames {
    const LEADERBOARDS = 'l';
    
    const LEADERBOARDS_BLACKLIST = 'b';
    
    const SPEEDRUN_LEADERBOARDS = 's';
    
    const SCORE_LEADERBOARDS = 'sc';
    
    const CUSTOM_LEADERBOARDS = 'cu';
    
    const CO_OP_LEADERBOARDS = 'co';
    
    const SEEDED_LEADERBOARDS = 'se';
    
    const DEATHLESS_LEADERBOARDS = 'd';
    
    const SCORE_TOTALS = 'sct';
    
    const SPEED_TOTALS = 'st';
    
    const DEATHLESS_TOTALS = 'dt';
    
    /* ---------- Leaderboards ---------- */ 
    
    public static function getLeaderboardsName() {
        return self::LEADERBOARDS;
    }
    
    public static function getLeaderboardPropertiesName() {
        return self::LEADERBOARDS . ':' . BaseCacheNames::PROPERTIES;
    }
    
    public static function getLeaderboardName($lbid) {
        return self::LEADERBOARDS . ":{$lbid}";
    }
    
    public static function getEntriesName($lbid) {
        return self::getLeaderboardName($lbid) . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getEntryPropertiesName($lbid) {
        return self::getEntriesName($lbid) . ':' . BaseCacheNames::PROPERTIES;
    }
    
    public static function getEntryName($lbid, $steamid) {
        return self::getEntriesName($lbid) . ":{$steamid}";
    }
    
    public static function getEntriesFilterName($lbid) {
        return self::getEntriesName($lbid) . ':' . BaseCacheNames::FILTER;
    }
    
    public static function getBlacklistName() {
        return self::getLeaderboardsName() . ':' . self::LEADERBOARDS_BLACKLIST;
    }
    
    public static function getSpeedrunLeaderboardsName() {
        return self::getLeaderboardsName() . ':' . self::SPEEDRUN_LEADERBOARDS;
    }
    
    public static function getScoreLeaderboardsName() {
        return self::getLeaderboardsName() . ':' . self::SCORE_LEADERBOARDS;
    }
    
    public static function getCustomLeaderboardsName() {
        return self::getLeaderboardsName() . ':' . self::CUSTOM_LEADERBOARDS;
    }
    
    public static function getCoOpLeaderboardsName() {
        return self::getLeaderboardsName() . ':' . self::CO_OP_LEADERBOARDS;
    }
    
    public static function getSeededLeaderboardsName() {
        return self::getLeaderboardsName() . ':' . self::SEEDED_LEADERBOARDS;
    }
    
    public static function getDeathlessLeaderboardsName() {
        return self::getLeaderboardsName() . ':' . self::DEATHLESS_LEADERBOARDS;
    }
    
    public static function getCharacterLeaderboardsName($character_name) {
        return self::getLeaderboardsName() . ':' . CharacterCacheNames::CHARACTERS . ":{$character_name}";
    }
    
    public static function getPowerLeaderboardsName() {
        return self::getLeaderboardsName() . ':' . RankingCacheNames::POWER_RANKING;
    }
    
    public static function getScoreTotalsName() {
        return self::getLeaderboardsName() . ':' . self::SCORE_TOTALS;
    }
    
    public static function getSpeedTotalsName() {
        return self::getLeaderboardsName() . ':' . self::SPEED_TOTALS;
    }
    
    public static function getDeathlessTotalsName() {
        return self::getLeaderboardsName() . ':' . self::SPEED_TOTALS;
    }
    
    public static function getLeaderboardScoreTotalsName($lbid) {
        return self::getLeaderboardName($lbid) . ':' . self::SCORE_TOTALS;
    }
    
    public static function getLeaderboardSpeedTotalsName($lbid) {
        return self::getLeaderboardName($lbid) . ':' . self::SPEED_TOTALS;
    }
    
    public static function getLeaderboardDeathlessTotalsName($lbid) {
        return self::getLeaderboardName($lbid) . ':' . self::SPEED_TOTALS;
    }
}