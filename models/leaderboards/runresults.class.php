<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class RunResults
extends Necrolab {
    protected static $run_results = array();
    
    public static function loadAll() {}
    
    public static function get($run_result) {
        static::loadAll();
        
        $run_result_id = NULL;
        
        if(isset(static::$run_results[$run_result])) {
            $run_result_id = static::$run_results[$run_result];
        }
        
        return $run_result_id;
    }
}