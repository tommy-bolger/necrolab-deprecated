<?php
namespace Modules\Necrolab\Models\Users\Youtube;

use \Modules\Necrolab\Models\Necrolab;

class Youtube
extends Necrolab {
    protected static $users = array();
    
    protected static $user_ids = array();
    
    protected static function load($youtube_id) {}
    
    public static function loadIds() {}
    
    public static function get($youtube_id) {
        static::load($youtube_id);
        
        return static::$users[$youtube_id];
    }
    
    public static function getId($youtube_id) {
        static::loadIds();
        
        $youtube_user_id = NULL;
        
        if(isset(static::$user_ids[$youtube_id])) {
            $youtube_user_id = static::$user_ids[$youtube_id];
        }
        
        return $youtube_user_id;
    }
    
    public static function addId($youtube_id, $youtube_user_id) {
        static::$user_ids[$youtube_id] = $youtube_user_id;
    }
}