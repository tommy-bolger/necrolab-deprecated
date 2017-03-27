<?php
namespace Modules\Necrolab\Models\Users\Reddit\Database;

use \Modules\Necrolab\Models\Users\Reddit\UserTokens as BaseUserTokens;
use \Modules\Necrolab\Models\Users\Reddit\Database\RecordModels\RedditUserToken as DatabaseRedditUserToken;

class UserTokens
extends BaseUserTokens {
    protected static function loadUnexpired($reddit_user_id) {
        if(empty(static::$unexpired_tokens[$reddit_user_id])) {
            $unexpired_token = db()->getRow("
                SELECT *
                FROM reddit_user_tokens
                WHERE reddit_user_id = :reddit_user_id
                    AND expired IS NULL
                    AND (
                        expires IS NULL
                        OR expires > :expires
                    )
            ", array(
                ':reddit_user_id' => $reddit_user_id,
                ':expires' => date('Y-m-d H:i:s')
            ));
            
            if(!empty($unexpired_token)) {
                static::$unexpired_tokens[$reddit_user_id] = $unexpired_token;
            }
        }
    }
    
    public static function expireKeys($reddit_user_id, $cache_query_name = NULL) {
        if(!empty($cache_query_name)) {
            $cache_query_name .= '_expire_keys';
        }
    
        db()->query("
            UPDATE reddit_user_tokens
            SET expired = :expired
            WHERE reddit_user_id = :reddit_user_id
                AND expired IS NULL
        ", array(
            ':expired' => date('Y-m-d H:i:s'),
            ':reddit_user_id' => $reddit_user_id
        ), array(), $cache_query_name);
    }

    public static function save($reddit_user_id, DatabaseRedditUserToken $user_token, $cache_query_name = NULL) {
        $user_token_record = static::getUnexpired($reddit_user_id);
        
        $reddit_user_token_id = NULL;
        
        if(empty($user_token_record)) {        
            $user_token->created = date('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
            
            $insert_record = $user_token->toArray();
            
            static::expireKeys($reddit_user_id, $cache_query_name);
        
            $reddit_user_token_id = db()->insert('reddit_user_tokens', $insert_record, $cache_query_name);
            
            static::addUnexpired($reddit_user_id, $insert_record);
        }
        else {
            $reddit_user_token_id = $user_token_record['reddit_user_token_id'];
        
            $update_record = $user_token->toArray();
            
            unset($update_record['reddit_user_id']);
            unset($update_record['created']);
            unset($update_record['token']);
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('reddit_user_tokens', $update_record, array(
                'reddit_user_token_id' => $reddit_user_token_id
            ), array(), $cache_query_name);
        }
        
        return $reddit_user_token_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE reddit_user_tokens;");
    }
}
