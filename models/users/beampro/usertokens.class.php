<?php
namespace Modules\Necrolab\Models\Users\Beampro;

use \Modules\Necrolab\Models\Necrolab;

class UserTokens
extends Necrolab {
    protected static $unexpired_tokens = array();
    
    protected static function loadUnexpired($beampro_user_id) {}
    
    public static function getUnexpired($beampro_user_id) {
        static::loadUnexpired($beampro_user_id);
    
        $unexpired_token_record = array();
        
        if(!empty(static::$unexpired_tokens[$beampro_user_id])) {
            $unexpired_token_record = static::$unexpired_tokens[$beampro_user_id];
        }
        
        return $unexpired_token_record;
    }
    
    protected static function addUnexpired($beampro_user_id, $unexpired_record) {
        static::$unexpired_tokens[$beampro_user_id] = $unexpired_record;
    }
}
