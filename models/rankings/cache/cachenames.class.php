<?php

namespace Modules\Necrolab\Models\Rankings\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;

class CacheNames
extends BaseCacheNames {            
    const POWER_RANKING = 'pr';
    
    const MODES = 'mo';
    
    const TOTAL_POINTS = 'tp';
    
    const SCORE = 'sc';
    
    const SPEED = 'sp';
    
    const DEATHLESS = 'de';
    
    const CHARACTER = 'ch';
    
    public static function getPowerRankingModesName($release_id, $seeded) {
        return self::POWER_RANKING . ":{$release_id}:{$seeded}:" . self::MODES;
    }
    
    public static function getPowerRankingName($release_id, $mode_id, $seeded) {
        return self::POWER_RANKING . ":{$release_id}:{$mode_id}:{$seeded}";
    }
    
    public static function getPowerEntriesName($release_id, $mode_id, $seeded) {
        return self::getPowerRankingName($release_id, $mode_id, $seeded) . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getPowerRankingEntryName($release_id, $mode_id, $seeded, $steam_user_id) {
        return self::getPowerEntriesName($release_id, $mode_id, $seeded) . ":{$steam_user_id}";
    }
    
    public static function getPowerTotalPointsName($release_id, $mode_id, $seeded) {
        return self::getPowerRankingName($release_id, $mode_id, $seeded) . ':' . self::TOTAL_POINTS;
    }
    
    public static function getScoreName($release_id, $mode_id, $seeded) {
        return self::getPowerRankingName($release_id, $mode_id, $seeded) . ':' . self::SCORE;
    }
    
    public static function getScorePointsName($release_id, $mode_id, $seeded) {
        return self::getScoreName($release_id, $mode_id, $seeded) . ':' . self::TOTAL_POINTS;
    }
    
    public static function getSpeedName($release_id, $mode_id, $seeded) {
        return self::getPowerRankingName($release_id, $mode_id, $seeded) . ':' . self::SPEED;
    }
    
    public static function getSpeedPointsName($release_id, $mode_id, $seeded) {
        return self::getSpeedName($release_id, $mode_id, $seeded) . ':' . self::TOTAL_POINTS;
    }
    
    public static function getDeathlessName($release_id, $mode_id, $seeded) {
        return self::getPowerRankingName($release_id, $mode_id, $seeded) . ':' . self::DEATHLESS;
    }
    
    public static function getDeathlessPointsName($release_id, $mode_id, $seeded) {
        return self::getDeathlessName($release_id, $mode_id, $seeded) . ':' . self::TOTAL_POINTS;
    }
    
    public static function getCharacterName($release_id, $mode_id, $seeded, $character_name) {
        return self::getPowerRankingName($release_id, $mode_id, $seeded) . ':' . self::CHARACTER . ":{$character_name}";
    }
    
    public static function getCharacterPointsName($release_id, $mode_id, $seeded, $character_name) {
        return self::getCharacterName($release_id, $mode_id, $seeded, $character_name) . ':' . self::TOTAL_POINTS;
    }
    
    public static function getIndexName($base_index_name, array $index_segments) {
        return parent::getIndexName("{$base_index_name}:" . BaseCacheNames::INDEX, $index_segments);
    }
}