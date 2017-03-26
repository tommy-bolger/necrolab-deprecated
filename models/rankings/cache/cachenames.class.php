<?php

namespace Modules\Necrolab\Models\Rankings\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;

class CacheNames
extends BaseCacheNames {            
    const POWER_RANKING = 'p';
    
    const MODES = 'mo';
    
    const TOTAL_POINTS = 'tp';
    
    const SCORE_POINTS = 'scp';
    
    const SPEED_POINTS = 'sp';
    
    const DEATHLESS_POINTS = 'dp';
    
    const CHARACTER_POINTS = 'cp';
    
    const CHARACTER = 'c';
    
    public static function getPowerRankingModesName($release_id) {
        return self::POWER_RANKING . ":{$release_id}:" . self::MODES;
    }
    
    public static function getPowerRankingName($release_id, $mode_id) {
        return self::POWER_RANKING . ":{$release_id}:{$mode_id}";
    }
    
    public static function getPowerEntriesName($release_id, $mode_id) {
        return self::getPowerRankingName($release_id, $mode_id) . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getPowerRankingEntryName($release_id, $mode_id, $steam_user_id) {
        return self::getPowerEntriesName($release_id, $mode_id) . ":{$steam_user_id}";
    }
    
    public static function getPowerTotalPointsName($release_id, $mode_id) {
        return self::getPowerRankingName($release_id, $mode_id) . ':' . self::TOTAL_POINTS;
    }
    
    public static function getScorePointsName($release_id, $mode_id) {
        return self::getPowerRankingName($release_id, $mode_id) . ':' . self::SCORE_POINTS;
    }
    
    public static function getSpeedPointsName($release_id, $mode_id) {
        return self::getPowerRankingName($release_id, $mode_id) . ':' . self::SPEED_POINTS;
    }
    
    public static function getDeathlessPointsName($release_id, $mode_id) {
        return self::getPowerRankingName($release_id, $mode_id) . ':' . self::DEATHLESS_POINTS;
    }
    
    public static function getCharacterPointsName($release_id, $mode_id, $character_name) {
        return self::getPowerRankingName($release_id, $mode_id) . ':' . self::CHARACTER . ":{$character_name}:" . self::CHARACTER_POINTS;
    }
}