<?php
namespace Modules\Necrolab\Models\Users\Discord;

use \Modules\Necrolab\Models\Necrolab;

class UserTokens
extends Necrolab {
    protected static $unexpired_tokens = array();
    
    protected static function loadUnexpired($discord_user_id) {}
    
    public static function getUnexpired($discord_user_id) {
        static::loadUnexpired($discord_user_id);
    
        $unexpired_token_record = array();
        
        if(!empty(static::$unexpired_tokens[$discord_user_id])) {
            $unexpired_token_record = static::$unexpired_tokens[$discord_user_id];
        }
        
        return $unexpired_token_record;
    }
    
    protected static function addUnexpired($discord_user_id, $unexpired_record) {
        static::$unexpired_tokens[$discord_user_id] = $unexpired_record;
    }
}
