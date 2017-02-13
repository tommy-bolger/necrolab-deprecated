<?php
namespace Modules\Necrolab\Models;

use \Exception;
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
    
    public static function generateRankPoints($rank) {
        return 1.7 / (log($rank / 100 + 1.03) / log(10));
    }
}