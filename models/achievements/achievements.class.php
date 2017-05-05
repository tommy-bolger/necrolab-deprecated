<?php
namespace Modules\Necrolab\Models\Achievements;

use \DateTime;
use \Exception;
use \Modules\Necrolab\Models\Necrolab;

class Achievements
extends Necrolab {
    protected static $achievements = array();
    
    protected static $ids_by_name = array();

    protected static function loadAll() {}
    
    public static function getAll() {
        static::loadAll();
        
        return static::$achievements;
    }
    
    public static function getById($achievement_id) {
        static::loadAll();
        
        $achievement_record = array();
        
        if(!isset(static::$achievements[$achievement_id])) {
            $achievement_record = static::$achievements[$achievement_id];
        }
        
        return $achievement_record;
    }
    
    public static function getIdByName($achievement_name) {
        static::loadAll();
    
        $achievement_id = NULL;

        if(isset(static::$ids_by_name[$achievement_name])) {
            $achievement_id = static::$ids_by_name[$achievement_name];
        }
        
        return $achievement_id;
    }
    
    public static function getByName($name) {
        static::loadAll();
        
        $achievement_record = array();
        
        if(!empty(static::$achievements)) {
            foreach(static::$achievements as $achievement) {
                if($achievement['name'] == $name) {
                    $achievement_record = $achievement;
                    
                    break;
                }
            }
        }
        
        return $achievement_record;
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name'],
            'description' => $data_row['description'],
            'icon_url' => $data_row['icon_url'],
            'icon_gray_url' => $data_row['icon_gray_url']
        );
    }
}