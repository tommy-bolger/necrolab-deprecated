<?php
namespace Modules\Necrolab\Models\Releases;

use \DateTime;
use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class Releases
extends Necrolab {
    protected static $releases = array();

    protected static function loadAll() {}
    
    public static function getAll() {
        static::loadAll();
        
        return static::$releases;
    }
    
    public static function getById($release_id) {
        static::loadAll();
        
        $release_record = array();
        
        if(!empty(static::$releases)) {
            foreach(static::$releases as $release) {
                if($release['release_id'] == $release_id) {
                    $release_record = $release;
                    
                    break;
                }
            }
        }
        
        return $release_record;
    }
    
    public static function getByName($name) {
        static::loadAll();
        
        $release_record = array();
        
        if(!empty(static::$releases)) {
            foreach(static::$releases as $release) {
                if($release['name'] == $name) {
                    $release_record = $release;
                    
                    break;
                }
            }
        }
        
        return $release_record;
    }
    
    public static function getByDate(DateTime $date) {
        static::loadAll();
        
        $release_records = array();
        
        if(!empty(static::$releases)) {        
            foreach(static::$releases as $release) {
                $start_date = new DateTime($release['start_date']);
                $end_date = new DateTime($release['end_date']);
            
                if($date >= $start_date && $date <= $end_date) {
                    $release_records[] = $release;
                }
            }
        }
        
        return $release_records;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name'],
            'start_date' => $data_row['start_date'],
            'end_date' => $data_row['end_date']
        );
    }
}