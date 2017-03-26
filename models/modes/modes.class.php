<?php
namespace Modules\Necrolab\Models\Modes;

use \DateTime;
use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class Modes
extends Necrolab {
    protected static $modes = array();

    protected static function loadAll() {}
    
    public static function getAll() {
        static::loadAll();
        
        return static::$modes;
    }
    
    public static function getById($mode_id) {
        static::loadAll();
        
        $mode_record = array();
        
        if(!empty(static::$modes)) {
            foreach(static::$modes as $mode) {
                if($mode['mode_id'] == $mode_id) {
                    $mode_record = $mode;
                    
                    break;
                }
            }
        }
        
        return $mode_record;
    }
    
    public static function getByName($name) {
        static::loadAll();
        
        $mode_record = array();
        
        if(!empty(static::$modes)) {
            foreach(static::$modes as $mode) {
                if($mode['name'] == $name) {
                    $mode_record = $mode;
                    
                    break;
                }
            }
        }
        
        return $mode_record;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'name' => $data_row['mode_name'],
            'display_name' => $data_row['mode_display_name']
        );
    }
}