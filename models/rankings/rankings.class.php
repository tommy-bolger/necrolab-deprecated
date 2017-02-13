<?php
namespace Modules\Necrolab\Models\Rankings;

use \DateTime;
use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class Rankings
extends Necrolab {
    protected static $rankings = array();

    protected static function load($release_id, DateTime $date) {}

    public static function get($release_id, DateTime $date) {
        static::load($release_id, $date);
        
        $date_formatted = $date->format('Y-m-d');
        
        $ranking = array();
        
        if(isset(static::$rankings[$release_id][$date_formatted])) {
            $ranking = static::$rankings[$release_id][$date_formatted];
        }
        
        return $ranking;
    }
    
    public static function getLastRefreshed() {
        return false;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return $data_row['date'];
    }
}