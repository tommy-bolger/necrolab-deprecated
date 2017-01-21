<?php

namespace Modules\Necrolab\Models\Rankings\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;

class CacheNames
extends BaseCacheNames {            
    const POWER_RANKING = 'p';
    
    const TOTAL_POINTS = 'tp';
    
    const SCORE_POINTS = 'scp';
    
    const SPEED_POINTS = 'sp';
    
    const DEATHLESS_POINTS = 'dp';
    
    const SCORE = 'sc';
    
    const SPEED = 's';
    
    const DEATHLESS = 'd';
    
    const RANKING = 'r';
    
    /* ---------- Power Rankings ---------- */
    
    public static function getPowerRankingName() {
        return self::POWER_RANKING;
    }
    
    public static function getPowerEntriesName() {
        return self::POWER_RANKING . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getPowerEntriesWildcardName() {
        return self::POWER_RANKING . ':' . BaseCacheNames::ENTRIES . ':*';
    }
    
    public static function getPowerRankingEntryName($steamid) {
        return self::getPowerEntriesName() . ":{$steamid}";
    }
    
    public static function getPowerEntriesFilterName() {
        return self::getPowerEntriesName() . ':' . BaseCacheNames::FILTER;
    }
    
    public static function getPowerTotalPointsName() {
        return self::POWER_RANKING . ':' . self::TOTAL_POINTS;
    }
    
    /* ---------- Score Rankings ---------- */
    
    public static function getScoreName() {
        return self::POWER_RANKING . ':' . self::SCORE;
    }
    
    public static function getScoreRankingName() {
        return self::getScoreName() . ':' . self::RANKING;
    }
    
    public static function getScoreEntriesName() {
        return self::getScoreName() . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getScoreEntryName($steamid) {
        return self::getScoreEntriesName() . ":{$steamid}";
    }
    
    public static function getScoreEntriesFilterName() {
        return self::getScoreEntriesName() . ':' . BaseCacheNames::FILTER;
    }
    
    public static function getScorePointsName() {
        return self::getScoreName() . ':' . self::SCORE_POINTS;
    }
    
    /* ---------- Speed Rankings ---------- */
    
    public static function getSpeedName() {
        return self::POWER_RANKING . ':' . self::SPEED;
    }
    
    public static function getSpeedRankingName() {
        return self::getSpeedName() . ':' . self::RANKING;
    }
    
    public static function getSpeedEntriesName() {
        return self::getSpeedName() . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getSpeedEntryName($steamid) {
        return self::getSpeedEntriesName() . ":{$steamid}";
    }
    
    public static function getSpeedEntriesFilterName() {
        return self::getSpeedEntriesName() . ':' . BaseCacheNames::FILTER;
    }
    
    public static function getSpeedPointsName() {
        return self::getSpeedName() . ':' . self::SPEED_POINTS;
    }
    
    /* ---------- Deathless Rankings ---------- */
    
    public static function getDeathlessName() {
        return self::POWER_RANKING . ':' . self::DEATHLESS;
    }
    
    public static function getDeathlessRankingName() {
        return self::getDeathlessName() . ':' . self::RANKING;
    }
    
    public static function getDeathlessEntriesName() {
        return self::getDeathlessName() . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getDeathlessEntryName($steamid) {
        return self::getDeathlessEntriesName() . ":{$steamid}";
    }
    
    public static function getDeathlessEntriesFilterName() {
        return self::getDeathlessEntriesName() . ':' . BaseCacheNames::FILTER;
    }
    
    public static function getDeathlessPointsName() {
        return self::getDeathlessName() . ':' . self::DEATHLESS_POINTS;
    }
}