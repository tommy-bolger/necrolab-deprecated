<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class RunResults
extends Necrolab {
    protected static $run_results = array();
    
    protected static $run_results_by_id = array();
    
    public static function loadAll() {}
    
    public static function get($run_result) {
        static::loadAll();
        
        $run_result_id = NULL;
        
        if(isset(static::$run_results[$run_result])) {
            $run_result_id = static::$run_results[$run_result];
        }
        
        return $run_result_id;
    }
    
    public static function getById($run_result_id) {
        static::loadAll();
        
        $run_result = NULL;
        
        if(isset(static::$run_results_by_id[$run_result_id])) {
            $run_result = static::$run_results_by_id[$run_result_id];
        }
        
        return $run_result;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return $data_row['run_result'];
    }
}