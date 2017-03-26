<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Modules\Necrolab\Models\Necrolab;

class Ranks
extends Necrolab {
    protected static $ranks_records = array();
    
    public static function loadAll() {}
    
    public static function getPoints($rank) {
        static::loadAll();
        
        $rank_points = NULL;
        
        if(isset(static::$ranks_records[$rank])) {
            $rank_points = static::$ranks_records[$rank];
        }
        
        return $rank_points;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return $data_row;
    }
}