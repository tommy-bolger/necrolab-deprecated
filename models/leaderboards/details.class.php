<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Modules\Necrolab\Models\Necrolab;

class Details
extends Necrolab {
    protected static $details_records = array();
    
    public static function loadAll() {}
    
    public static function get($details) {
        static::loadAll();
        
        $leaderboard_entry_details_id = NULL;
        
        if(isset(static::$details_records[$details])) {
            $leaderboard_entry_details_id = static::$details_records[$details];
        }
        
        return $leaderboard_entry_details_id;
    }
}