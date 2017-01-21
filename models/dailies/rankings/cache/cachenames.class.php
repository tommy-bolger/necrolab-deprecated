<?php

namespace Modules\Necrolab\Models\Dailies\Rankings\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;
use \Modules\Necrolab\Models\Leaderboards\Cache\CacheNames as LeaderboardCacheNames;

class CacheNames
extends BaseCacheNames {            
    const DAILY_RANKINGS = 'da';
    
    const ALL_TIME = 'at';
    
    const DAY_TYPE = 'd';
    
    const TOTAL_POINTS = 'tp';
    
    const RANK_SUM = 'rs';
    
    const TOTAL_DAILIES = 'td';
    
    const DAY_TYPES = 'dt';
    
    public static function getRankingsBaseName() {
        return self::DAILY_RANKINGS;
    }
    
    public static function getDayTypesName() {
        return self::getRankingsBaseName() . ':' . self::DAY_TYPE;
    }
    
    public static function getRankingsName($number_of_days = NULL) {
        $entry_name = NULL;
        
        if(empty($number_of_days)) {
            $entry_name = self::getRankingsBaseName() . ':' . self::ALL_TIME;
        }
        else {
            $entry_name = self::getRankingsBaseName() . ":{$number_of_days}" . self::DAY_TYPE;
        }
        
        return $entry_name;
    }

    public static function getEntriesName($number_of_days = NULL) {
        return self::getRankingsName($number_of_days) . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getEntryName($steamid, $number_of_days = NULL) {    
        return self::getEntriesName($number_of_days) . ":{$steamid}";
    }
    
    public static function getEntriesFilterName($number_of_days = NULL) {    
        return self::getEntriesName($number_of_days) . ':' . BaseCacheNames::FILTER;
    }
    
    public static function getTotalPointsName($number_of_days = NULL) {    
        return self::getRankingsName($number_of_days) . ':' . self::TOTAL_POINTS;
    }
    
    public static function getRankSumName($number_of_days = NULL) {    
        return self::getRankingsName($number_of_days) . ':' . self::RANK_SUM;
    }
    
    public static function getTotalDailiesName($number_of_days = NULL) {    
        return self::getRankingsName($number_of_days) . ':' . self::TOTAL_DAILIES;
    }
    
    public static function getDayTypeName($number_of_days = NULL) {    
        return self::getRankingsName($number_of_days) . ':' . self::DAY_TYPES;
    }
    
    public static function getLastUpdatedName($number_of_days = NULL) {    
        return self::getRankingsName($number_of_days) . ':' . BaseCacheNames::LAST_UPDATED;
    }
}