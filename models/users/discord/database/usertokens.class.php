<?php
namespace Modules\Necrolab\Models\Users\Discord\Database;

use \Modules\Necrolab\Models\Users\Discord\UserTokens as BaseUserTokens;
use \Modules\Necrolab\Models\Users\Discord\Database\RecordModels\DiscordUserToken as DatabaseDiscordUserToken;

class UserTokens
extends BaseUserTokens {
    protected static function loadUnexpired($discord_user_id) {
        if(empty(static::$unexpired_tokens[$discord_user_id])) {
            $unexpired_token = db()->getRow("
                SELECT *
                FROM discord_user_tokens
                WHERE discord_user_id = :discord_user_id
                    AND expired IS NULL
                    AND (
                        expires IS NULL
                        OR expires > :expires
                    )
            ", array(
                ':discord_user_id' => $discord_user_id,
                ':expires' => date('Y-m-d H:i:s')
            ));
            
            if(!empty($unexpired_token)) {
                static::$unexpired_tokens[$discord_user_id] = $unexpired_token;
            }
        }
    }
    
    public static function expireKeys($discord_user_id, $cache_query_name = NULL) {
        if(!empty($cache_query_name)) {
            $cache_query_name .= '_expire_keys';
        }
    
        db()->query("
            UPDATE discord_user_tokens
            SET expired = :expired
            WHERE discord_user_id = :discord_user_id
                AND expired IS NULL
        ", array(
            ':expired' => date('Y-m-d H:i:s'),
            ':discord_user_id' => $discord_user_id
        ), array(), $cache_query_name);
    }

    public static function save($discord_user_id, DatabaseDiscordUserToken $user_token, $cache_query_name = NULL) {
        $user_token_record = static::getUnexpired($discord_user_id);
        
        $discord_user_token_id = NULL;
        
        if(empty($user_token_record)) {        
            $user_token->created = date('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
            
            $insert_record = $user_token->toArray();
            
            static::expireKeys($discord_user_id, $cache_query_name);
        
            $discord_user_token_id = db()->insert('discord_user_tokens', $insert_record, $cache_query_name);
            
            static::addUnexpired($discord_user_id, $insert_record);
        }
        else {
            $discord_user_token_id = $user_token_record['discord_user_token_id'];
        
            $update_record = $user_token->toArray();
            
            unset($update_record['discord_user_id']);
            unset($update_record['created']);
            unset($update_record['token']);
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('discord_user_tokens', $update_record, array(
                'discord_user_token_id' => $discord_user_token_id
            ), array(), $cache_query_name);
        }
        
        return $discord_user_token_id;
    }
}
