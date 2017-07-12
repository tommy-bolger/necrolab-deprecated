<?php
namespace Modules\Necrolab\Models\Rankings;

use \DateTime;
use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class Rankings
extends Necrolab {
    protected static $rankings = array();

    protected static function load($release_id, $mode_id, $seeded, DateTime $date) {}

    public static function get($release_id, $mode_id, $seeded, DateTime $date) {
        static::load($release_id, $mode_id, $seeded, $date);
        
        $date_formatted = $date->format('Y-m-d');
        
        $ranking = array();
        
        if(isset(static::$rankings[$release_id][$mode_id][$seeded][$date_formatted])) {
            $ranking = static::$rankings[$release_id][$mode_id][$seeded][$date_formatted];
        }
        
        return $ranking;
    }
    
    public static function getLastRefreshed() {
        return false;
    }
    
    public static function getGenerateQueueName() {
        return 'power_ranking_generation';
    }
    
    public static function addToGenerateQueue(DateTime $date) {        
        static::addDateToQueue(static::getGenerateQueueName(), $date);
    }
    
    public static function getCacheQueueName() {
        return 'power_ranking_cache';
    }
    
    public static function addToCacheQueue(DateTime $date) {        
        static::addDateToQueue(static::getCacheQueueName(), $date);
    }
    
    public static function getFormattedApiRecord($data_row) {
        return $data_row['date'];
    }
}