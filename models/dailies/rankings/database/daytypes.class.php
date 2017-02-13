<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \Framework\Data\ResultSet\SQL;
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
    
    public static function getAllBaseResultset() {    
        $resultset = new SQL("daily_ranking_day_types");
        
        $resultset->setBaseQuery("
            SELECT *
            FROM daily_ranking_day_types
            {{WHERE_CRITERIA}}
        ");
        
        return $resultset;
    }
    
    public static function getAllEnabledResultset() {    
        $resultset = static::getAllBaseResultset();
        
        $resultset->addFilterCriteria('enabled = 1');
        
        return $resultset;
    }
}