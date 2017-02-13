<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \DateTime;
use \Modules\Necrolab\Models\Necrolab;

class Snapshots
extends Necrolab {
    protected static $snapshots = array();
    
    protected static function loadDate(DateTime $date) {}
    
    public static function get($lbid, DateTime $date) {
        static::loadDate($date);
        
        $date_formatted = $date->format('Y-m-d');
        
        $snapshot_record = array();
        
        if(!empty(static::$snapshots[$date_formatted][$lbid])) {
            $snapshot_record = static::$snapshots[$date_formatted][$lbid];
        }
        
        return $snapshot_record;
    }
    
    public static function getAll(DateTime $date) {
        static::loadDate($date);
        
        $date_formatted = $date->format('Y-m-d');
        
        $snapshot_records = array();
        
        if(!empty(static::$snapshots[$date_formatted])) {
            $snapshot_records = static::$snapshots[$date_formatted];
        }
        
        return $snapshot_records;
    }
    
    public static function getFormattedApiRecord(array $data_row) {
        return $data_row['date'];
    }
}