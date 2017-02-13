<?php
namespace Modules\Necrolab\Models\Users\Twitter;

use \Modules\Necrolab\Models\Necrolab;

class UserTokens
extends Necrolab {
    protected static $unexpired_tokens = array();
    
    protected static function loadUnexpired($twitter_user_id) {}
    
    public static function getUnexpired($twitter_user_id) {
        static::loadUnexpired($twitter_user_id);
    
        $unexpired_token_record = array();
        
        if(!empty(static::$unexpired_tokens[$twitter_user_id])) {
            $unexpired_token_record = static::$unexpired_tokens[$twitter_user_id];
        }
        
        return $unexpired_token_record;
    }
    
    protected static function addUnexpired($twitter_user_id, $unexpired_record) {
        static::$unexpired_tokens[$twitter_user_id] = $unexpired_record;
    }
}
