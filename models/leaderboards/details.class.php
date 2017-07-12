<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Modules\Necrolab\Models\Necrolab;

class Details
extends Necrolab {
    protected static $details_records = array();
    
    protected static $details_records_by_id = array();
    
    public static function loadAll() {}
    
    public static function get($details) {
        static::loadAll();
        
        $leaderboard_entry_details_id = NULL;
        
        if(isset(static::$details_records[$details])) {
            $leaderboard_entry_details_id = static::$details_records[$details];
        }
        
        return $leaderboard_entry_details_id;
    }
    
    public static function getById($leaderboard_entry_details_id) {
        static::loadAll();
        
        $details = NULL;
        
        if(isset(static::$details_records_by_id[$leaderboard_entry_details_id])) {
            $details = static::$details_records_by_id[$leaderboard_entry_details_id];
        }
        
        return $details;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return $data_row['details'];
    }
}