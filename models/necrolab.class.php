<?php
namespace Modules\Necrolab\Models;

use \DateTime;
use \DateInterval;
use \Framework\Core\Framework;
use \Framework\Modules\Module;

class Necrolab {
    protected static $lua_script_path;
    
    protected static function getPartitionTableNames($base_name, $start_date, $end_date) {
        $partition_table_names = array();
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            $partition_table_names[] = "{$base_name}_{$current_date->format('Y_m')}";
        
            $current_date->add(new DateInterval('P1M'));
        }
        
        return $partition_table_names;
    }
    
    public static function getLuaScriptPath() {
        if(empty(self::$lua_script_path)) {
            self::$lua_script_path = Module::getInstance('necrolab')->getScriptFilePath() . '/lua';
        }
        
        return self::$lua_script_path;
    }
    
    public static function roundNumber($unrounded_number) {    
        $rounded_number = NULL;
    
        if(!empty($unrounded_number)) {
            $rounding_place = 1;
            $unrounded_number_split = explode('.', (string)$unrounded_number);
            
            $left_decimal = $unrounded_number_split[0];
        
            if(strlen($left_decimal) == 2) {
                $rounding_place = 2;
            }
            
            if(strlen($left_decimal) == 1) {
                $left_decimal = (int)$left_decimal;
            
                if($left_decimal < 1) {
                    $rounding_place = 5;
                }
                else {
                    $rounding_place = 3;
                }
            }
        
            $rounded_number = round($unrounded_number, $rounding_place);
        }
    
        return $rounded_number;
    }
}