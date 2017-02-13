<?php
namespace Modules\Necrolab\Models\Users\Beampro;

use \Modules\Necrolab\Models\Necrolab;

class Beampro
extends Necrolab {
    protected static $users = array();
    
    protected static $user_ids = array();
    
    protected static function load($beampro_id) {}
    
    public static function loadIds() {}
    
    public static function get($beampro_id) {
        static::load($beampro_id);
        
        return static::$users[$beampro_id];
    }
    
    public static function getId($beampro_id) {
        static::loadIds();
        
        $beampro_user_id = NULL;
        
        if(isset(static::$user_ids[$beampro_id])) {
            $beampro_user_id = static::$user_ids[$beampro_id];
        }
        
        return $beampro_user_id;
    }
    
    public static function addId($beampro_id, $beampro_user_id) {
        static::$user_ids[$beampro_id] = $beampro_user_id;
    }
}