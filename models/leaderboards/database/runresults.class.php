<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \Exception;
use \Modules\Necrolab\Models\Leaderboards\RunResults as BaseRunResults;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\RunResult;

class RunResults
extends BaseRunResults {    
    public static function loadAll() {
        if(empty(static::$run_results)) {            
            static::$run_results = db()->getMappedColumn("
                SELECT 
                    name,
                    run_result_id
                FROM run_results
            ");
        }
    }
    
    public static function save(RunResult $run_result) {
        $run_result_id = static::get($run_result->name);
        
        if(empty($run_result_id)) {        
            $run_result_id = db()->insert('run_results', $run_result->toArray(), 'run_result_insert');
            
            static::$run_results[$run_result->name] = $run_result_id;
        }
    
        return $run_result_id;
    }
    
    public static function updateBatch($run_result_id, RunResult $run_result) { 
        $array_record = $run_result->toArray();
        
        db()->update('run_results', $run_result->toArray(), array(
            'run_result_id' => $run_result_id
        ), '', 'run_result_update');
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'rr.name',
                'alias' => 'run_result'
            )
        ));
    }
}