<?php
namespace Modules\Necrolab\Models\Users\Twitch;

use \Modules\Necrolab\Models\Necrolab;

class Twitch
extends Necrolab {
    protected static $users = array();
    
    protected static $user_ids = array();
    
    protected static function load($twitch_id) {}
    
    public static function loadIds() {}
    
    public static function get($twitch_id) {
        static::load($twitch_id);
        
        return static::$users[$twitch_id];
    }
    
    public static function getId($twitch_id) {
        static::loadIds();
        
        $twitch_user_id = NULL;
        
        if(isset(static::$user_ids[$twitch_id])) {
            $twitch_user_id = static::$user_ids[$twitch_id];
        }
        
        return $twitch_user_id;
    }
    
    public static function addId($twitch_id, $twitch_user_id) {
        static::$user_ids[$twitch_id] = $twitch_user_id;
    }

    public static function getProfileUrl($twitch_username) {
        return "https://www.twitch.tv/{$twitch_username}";        
    }
}
