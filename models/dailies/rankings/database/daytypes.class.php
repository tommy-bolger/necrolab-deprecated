<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \Modules\Necrolab\Models\Dailies\Rankings\DayTypes as BaseDayTypes;

class DayTypes
extends BaseDayTypes {
    public static function loadAll() {
        if(empty(static::$day_types)) {        
            $day_types = db()->getGroupedRows("
                SELECT 
                    number_of_days,
                    *
                FROM daily_ranking_day_types
            ");
            
            if(!empty($day_types)) {
                static::$day_types = $day_types;
            }
        }
    }
}