<?php
namespace Modules\Necrolab\Models\DailyRankings\Cache;

use \Modules\Necrolab\Models\Dailies\DailyTypes as BaseDayTypes;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\DayTypes as DatabaseDayTypes;

class DayTypes
extends BaseDayTypes { 
    public static function loadDayTypes() {
        if(empty(static::$day_types)) {
            static::$day_types = cache('read')->hGetAll(CacheNames::getDayTypesName());
        }
    }
    
    public static function loadActiveDayTypes() {
        if(empty(static::$active_day_types)) {
            static::loadDayTypes();
            
            static::$active_day_types = static::$day_types;
        }
    }
    
    public static function loadIntoCache($cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }

        $day_types = DatabaseDayTypes::getActiveDayTypes();
        
        if(!empty($day_types)) {
            foreach($day_types as $day_type) {
                $cache->hSet(CacheNames::getDayTypesName(), $day_type['number_of_days'], $day_type['daily_ranking_day_type_id']);
            }
        }
    }
}