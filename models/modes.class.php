<?php
namespace Modules\Necrolab\Models;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;

class Modes
extends Necrolab {
    protected static $modes = array();

    protected static function loadAll() {        
        if(empty(static::$modes)) {
            $modes = array();
            
            $cache_key = 'modes';
            
            $local_cache = cache('local');
        
            $modes = $local_cache->get($cache_key);
        
            if(empty($modes)) {
                $modes = db()->getAll("
                    SELECT *
                    FROM modes
                    ORDER BY sort_order ASC
                ");
                
                if(!empty($modes)) {
                    $local_cache->set($cache_key, $modes, NULL, 86400);
                }
            }
            
            if(!empty($modes)) {
                static::$modes = $modes;
            }
        }
    }
    
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
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name']
        );
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE modes;");
    }
    
    public static function getBaseResultset() {    
        $resultset = new SQL("modes");
        
        static::setSelectFields($resultset);
        
        $resultset->setFromTable('modes mo');
        
        $resultset->addSortCriteria('sort_order', 'ASC');
        
        return $resultset;
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'mo.name',
                'alias' => 'mode_name'
            ),
            array(
                'field' => 'mo.display_name',
                'alias' => 'mode_display_name',
            )
        ));
    }
}