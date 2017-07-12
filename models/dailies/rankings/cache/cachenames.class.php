<?php

namespace Modules\Necrolab\Models\Dailies\Rankings\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;

class CacheNames
extends BaseCacheNames {            
    const DAILY_RANKINGS = 'da';
    
    const DAY_TYPE = 'd';
    
    const TOTAL_POINTS = 'tp';
    
    const MODES = 'mo';
    
    const NUMBER_OF_DAYS = 'nd';
    
    public static function getModesName($release_id) {
        return self::DAILY_RANKINGS . ":{$release_id}:" . self::MODES;
    }
    
    public static function getRankingsBaseName($release_id, $mode_id) {
        return self::DAILY_RANKINGS . ":{$release_id}:{$mode_id}";
    }
    
    public static function getModeNumberOfDaysName($release_id, $mode_id) {
        return self::getRankingsBaseName($release_id, $mode_id) . ':' . self::NUMBER_OF_DAYS;
    }
    
    public static function getRankingsName($release_id, $mode_id, $number_of_days) {
        return self::getRankingsBaseName($release_id, $mode_id) . ":{$number_of_days}";
    }

    public static function getEntriesName($release_id, $mode_id, $number_of_days) {
        return self::getRankingsName($release_id, $mode_id, $number_of_days) . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getEntryName($release_id, $mode_id, $number_of_days, $steamid) {    
        return self::getEntriesName($release_id, $mode_id, $number_of_days) . ":{$steamid}";
    }
    
    public static function getTotalPointsName($release_id, $mode_id, $number_of_days) {    
        return self::getRankingsName($release_id, $mode_id, $number_of_days) . ':' . self::TOTAL_POINTS;
    }
    
    public static function getEntriesIndexName($release_id, $mode_id, $number_of_days, array $index_segments = array()) {
        return parent::getIndexName(self::getEntriesName($release_id, $mode_id, $number_of_days) . ':' . BaseCacheNames::INDEX, $index_segments);
    }
}