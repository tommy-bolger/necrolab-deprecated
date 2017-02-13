<?php
namespace Modules\Necrolab\Models\Users\Youtube\Database;

use \Modules\Necrolab\Models\Users\Youtube\UserTokens as BaseUserTokens;
use \Modules\Necrolab\Models\Users\Youtube\Database\RecordModels\YoutubeUserToken as DatabaseYoutubeUserToken;

class UserTokens
extends BaseUserTokens {
    protected static function loadUnexpired($youtube_user_id) {
        if(empty(static::$unexpired_tokens[$youtube_user_id])) {
            $unexpired_token = db()->getRow("
                SELECT *
                FROM youtube_user_tokens
                WHERE youtube_user_id = :youtube_user_id
                    AND expired IS NULL
                    AND (
                        expires IS NULL
                        OR expires > :expires
                    )
            ", array(
                ':youtube_user_id' => $youtube_user_id,
                ':expires' => date('Y-m-d H:i:s')
            ));
            
            if(!empty($unexpired_token)) {
                static::$unexpired_tokens[$youtube_user_id] = $unexpired_token;
            }
        }
    }
    
    public static function expireKeys($youtube_user_id, $cache_query_name = NULL) {
        if(!empty($cache_query_name)) {
            $cache_query_name .= '_expire_keys';
        }
    
        db()->query("
            UPDATE youtube_user_tokens
            SET expired = :expired
            WHERE youtube_user_id = :youtube_user_id
                AND expired IS NULL
        ", array(
            ':expired' => date('Y-m-d H:i:s'),
            ':youtube_user_id' => $youtube_user_id
        ), array(), $cache_query_name);
    }

    public static function save($youtube_user_id, DatabaseYoutubeUserToken $user_token, $cache_query_name = NULL) {
        $user_token_record = static::getUnexpired($youtube_user_id);
        
        $youtube_user_token_id = NULL;
        
        if(empty($user_token_record)) {        
            $user_token->created = date('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
            
            $insert_record = $user_token->toArray();
            
            static::expireKeys($youtube_user_id, $cache_query_name);
        
            $youtube_user_token_id = db()->insert('youtube_user_tokens', $insert_record, $cache_query_name);
            
            static::addUnexpired($youtube_user_id, $insert_record);
        }
        else {
            $youtube_user_token_id = $user_token_record['youtube_user_token_id'];
        
            $update_record = $user_token->toArray();
            
            unset($update_record['youtube_user_id']);
            unset($update_record['created']);
            unset($update_record['token']);
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('youtube_user_tokens', $update_record, array(
                'youtube_user_token_id' => $youtube_user_token_id
            ), array(), $cache_query_name);
        }
        
        return $youtube_user_token_id;
    }
}
