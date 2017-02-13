<?php
namespace Modules\Necrolab\Models\Users\Twitter;

use \Modules\Necrolab\Models\Necrolab;

class Twitter
extends Necrolab {
    protected static $users = array();
    
    protected static $user_ids = array();
    
    protected static function load($twitter_id) {}
    
    public static function loadIds() {}
    
    public static function get($twitter_id) {
        static::load($twitter_id);
        
        return static::$users[$twitter_id];
    }
    
    public static function getId($twitter_id) {
        static::loadIds();
        
        $twitter_user_id = NULL;
        
        if(isset(static::$user_ids[$twitter_id])) {
            $twitter_user_id = static::$user_ids[$twitter_id];
        }
        
        return $twitter_user_id;
    }
    
    public static function addId($twitter_id, $twitter_user_id) {
        static::$user_ids[$twitter_id] = $twitter_user_id;
    }
}