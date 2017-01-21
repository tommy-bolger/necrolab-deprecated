<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Modules\Necrolab\Models\Necrolab;

class Blacklist
extends Necrolab {
    protected static $blacklist = array();
    
    public static function load() {}
    
    public static function getRecordById($lbid) {
        static::load();
        
        $blacklist_record = NULL;
        
        if(isset(static::$blacklist[$lbid])) {
            $blacklist_record = static::$blacklist[$lbid];
        }
        
        return $blacklist_record;
    }
}