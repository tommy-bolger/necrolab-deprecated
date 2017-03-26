<?php
namespace Modules\Necrolab\Models\Dailies\Rankings;

use \DateTime;
use \DateInterval;
use \Framework\Modules\Module;
use \Modules\Necrolab\Models\Necrolab;

class DayTypes
extends Necrolab {
    protected static $day_types = array();
    
    protected static $active_day_types = array();
    
    public static function loadAll() {}
    
    public static function loadActive() {
        if(empty(static::$active_day_types)) {
            static::loadAll();
            
            if(!empty(static::$day_types)) {
                foreach(static::$day_types as $number_of_days => $day_type) {
                    if(!empty($day_type['enabled'])) {
                        static::$active_day_types[$number_of_days] = $day_type;
                    }
                }
            }
        }
    }
    
    public static function getAll() {
        static::loadAll();
        
        return static::$day_types;
    }
    
    public static function getActive() {
        static::loadActive();
        
        return static::$active_day_types;
    }
    
    public static function getActiveForDate(DateTime $date) {
        static::loadActive();
        
        $active_day_types = array();
        
        if(!empty(static::$active_day_types)) {
            foreach(static::$active_day_types as $active_day_type) {
                $number_of_days = $active_day_type['number_of_days'];
                
                if($number_of_days == 0) {
                    $number_of_days = $date->diff(new DateTime(Module::getInstance('necrolab')->configuration->steam_live_launch_date))->format('%a');
                }
            
                $day_type_start_date = clone $date;
                
                $day_type_start_date->sub(new DateInterval("P{$number_of_days}D"));
                
                $active_day_type['start_date'] = $day_type_start_date;
            
                $active_day_types[$active_day_type['daily_ranking_day_type_id']] = $active_day_type;
            }
        }
        
        return $active_day_types; 
    }
}