<?php
namespace Modules\Necrolab\Models\Users\Twitter\Database;

use \Modules\Necrolab\Models\Users\Twitter\UserTokens as BaseUserTokens;
use \Modules\Necrolab\Models\Users\Twitter\Database\RecordModels\TwitterUserToken as DatabaseTwitterUserToken;

class UserTokens
extends BaseUserTokens {
    protected static function loadUnexpired($twitter_user_id) {
        if(empty(static::$unexpired_tokens[$twitter_user_id])) {
            $unexpired_token = db()->getRow("
                SELECT *
                FROM twitter_user_tokens
                WHERE twitter_user_id = :twitter_user_id
                    AND expired IS NULL
                    AND (
                        expires IS NULL
                        OR expires > :expires
                    )
            ", array(
                ':twitter_user_id' => $twitter_user_id,
                ':expires' => date('Y-m-d H:i:s')
            ));
            
            if(!empty($unexpired_token)) {
                static::$unexpired_tokens[$twitter_user_id] = $unexpired_token;
            }
        }
    }
    
    public static function expireKeys($twitter_user_id, $cache_query_name = NULL) {
        if(!empty($cache_query_name)) {
            $cache_query_name .= '_expire_keys';
        }
    
        db()->query("
            UPDATE twitter_user_tokens
            SET expired = :expired
            WHERE twitter_user_id = :twitter_user_id
                AND expired IS NULL
        ", array(
            ':expired' => date('Y-m-d H:i:s'),
            ':twitter_user_id' => $twitter_user_id
        ), array(), $cache_query_name);
    }

    public static function save($twitter_user_id, DatabaseTwitterUserToken $user_token, $cache_query_name = NULL) {
        $user_token_record = static::getUnexpired($twitter_user_id);
        
        $twitter_user_token_id = NULL;
        
        if(empty($user_token_record)) {        
            $user_token->created = date('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
            
            $insert_record = $user_token->toArray();
            
            static::expireKeys($twitter_user_id, $cache_query_name);
        
            $twitter_user_token_id = db()->insert('twitter_user_tokens', $insert_record, $cache_query_name);
            
            static::addUnexpired($twitter_user_id, $insert_record);
        }
        else {
            $twitter_user_token_id = $user_token_record['twitter_user_token_id'];
        
            $update_record = $user_token->toArray();
            
            unset($update_record['twitter_user_id']);
            unset($update_record['created']);
            unset($update_record['identifier']);
            unset($update_record['secret']);
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('twitter_user_tokens', $update_record, array(
                'twitter_user_token_id' => $twitter_user_token_id
            ), array(), $cache_query_name);
        }
        
        return $twitter_user_token_id;
    }
}
