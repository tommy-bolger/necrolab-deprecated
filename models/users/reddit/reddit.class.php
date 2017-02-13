<?php
namespace Modules\Necrolab\Models\Users\Reddit;

use \Modules\Necrolab\Models\Necrolab;

class Reddit
extends Necrolab {
    protected static $users = array();
    
    protected static $user_ids = array();
    
    protected static function load($reddit_id) {}
    
    public static function loadIds() {}
    
    public static function get($reddit_id) {
        static::load($reddit_id);
        
        return static::$users[$reddit_id];
    }
    
    public static function getId($reddit_id) {
        static::loadIds();
        
        $reddit_user_id = NULL;
        
        if(isset(static::$user_ids[$reddit_id])) {
            $reddit_user_id = static::$user_ids[$reddit_id];
        }
        
        return $reddit_user_id;
    }
    
    public static function addId($reddit_id, $reddit_user_id) {
        static::$user_ids[$reddit_id] = $reddit_user_id;
    }

    public static function getProfileUrl($reddit_username) {
        return "https://www.reddit.com/u/{$reddit_username}";        
    }
}
