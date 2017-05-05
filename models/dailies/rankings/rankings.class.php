<?php
namespace Modules\Necrolab\Models\Dailies\Rankings;

use \DateTime;
use \Modules\Necrolab\Models\Necrolab;

class Rankings
extends Necrolab {    
    protected static $rankings = array();

    protected static function load($release_id, $mode_id, $daily_ranking_day_type_id, DateTime $date) {}

    public static function get($release_id, $mode_id, $daily_ranking_day_type_id, DateTime $date) {
        static::load($release_id, $mode_id, $daily_ranking_day_type_id, $date);
        
        $date_formatted = $date->format('Y-m-d');
        
        $ranking = array();
        
        if(isset(static::$rankings[$release_id][$mode_id][$date_formatted][$daily_ranking_day_type_id])) {
            $ranking = static::$rankings[$release_id][$mode_id][$date_formatted][$daily_ranking_day_type_id];
        }
        
        return $ranking;
    }
    
    public static function getGenerateQueueName() {
        return 'daily_ranking_generation';
    }
    
    public static function addToGenerateQueue(DateTime $date) {
        static::addDateToQueue(static::getGenerateQueueName(), $date);
    }
    
    public static function getFormattedApiRecord($data_row) {
        return $data_row['date'];
    }
}