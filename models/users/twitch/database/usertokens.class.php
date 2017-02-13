<?php
namespace Modules\Necrolab\Models\Users\Twitch\Database;

use \Modules\Necrolab\Models\Users\Twitch\UserTokens as BaseUserTokens;
use \Modules\Necrolab\Models\Users\Twitch\Database\RecordModels\TwitchUserToken as DatabaseTwitchUserToken;

class UserTokens
extends BaseUserTokens {
    protected static function loadUnexpired($twitch_user_id) {
        if(empty(static::$unexpired_tokens[$twitch_user_id])) {
            $unexpired_token = db()->getRow("
                SELECT *
                FROM twitch_user_tokens
                WHERE twitch_user_id = :twitch_user_id
                    AND expired IS NULL
                    AND (
                        expires IS NULL
                        OR expires > :expires
                    )
            ", array(
                ':twitch_user_id' => $twitch_user_id,
                ':expires' => date('Y-m-d H:i:s')
            ));
            
            if(!empty($unexpired_token)) {
                static::$unexpired_tokens[$twitch_user_id] = $unexpired_token;
            }
        }
    }
    
    public static function expireKeys($twitch_user_id, $cache_query_name = NULL) {
        if(!empty($cache_query_name)) {
            $cache_query_name .= '_expire_keys';
        }
    
        db()->query("
            UPDATE twitch_user_tokens
            SET expired = :expired
            WHERE twitch_user_id = :twitch_user_id
                AND expired IS NULL
        ", array(
            ':expired' => date('Y-m-d H:i:s'),
            ':twitch_user_id' => $twitch_user_id
        ), array(), $cache_query_name);
    }

    public static function save($twitch_user_id, DatabaseTwitchUserToken $user_token, $cache_query_name = NULL) {
        $user_token_record = static::getUnexpired($twitch_user_id);
        
        $twitch_user_token_id = NULL;
        
        if(empty($user_token_record)) {        
            $user_token->created = date('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
            
            $insert_record = $user_token->toArray();
            
            static::expireKeys($twitch_user_id, $cache_query_name);
        
            $twitch_user_token_id = db()->insert('twitch_user_tokens', $insert_record, $cache_query_name);
            
            static::addUnexpired($twitch_user_id, $insert_record);
        }
        else {
            $twitch_user_token_id = $user_token_record['twitch_user_token_id'];
        
            $update_record = $user_token->toArray();
            
            unset($update_record['twitch_user_id']);
            unset($update_record['created']);
            unset($update_record['token']);
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('twitch_user_tokens', $update_record, array(
                'twitch_user_token_id' => $twitch_user_token_id
            ), array(), $cache_query_name);
        }
        
        return $twitch_user_token_id;
    }
}
