<?php
namespace Modules\Necrolab\Models\Users\Discord;

use \Modules\Necrolab\Models\Necrolab;

class Discord
extends Necrolab {
    protected static $users = array();
    
    protected static $user_ids = array();
    
    protected static function load($discord_id) {}
    
    public static function loadIds() {}
    
    public static function get($discord_id) {
        static::load($discord_id);
        
        return static::$users[$discord_id];
    }
    
    public static function getId($discord_id) {
        static::loadIds();
        
        $discord_user_id = NULL;
        
        if(isset(static::$user_ids[$discord_id])) {
            $discord_user_id = static::$user_ids[$discord_id];
        }
        
        return $discord_user_id;
    }
    
    public static function addId($discord_id, $discord_user_id) {
        static::$user_ids[$discord_id] = $discord_user_id;
    }
}