<?php
namespace Modules\Necrolab\Models\Users\Beampro\Database;

use \Modules\Necrolab\Models\Users\Beampro\UserTokens as BaseUserTokens;
use \Modules\Necrolab\Models\Users\Beampro\Database\RecordModels\BeamproUserToken as DatabaseBeamproUserToken;

class UserTokens
extends BaseUserTokens {
    protected static function loadUnexpired($beampro_user_id) {
        if(empty(static::$unexpired_tokens[$beampro_user_id])) {
            $unexpired_token = db()->getRow("
                SELECT *
                FROM beampro_user_tokens
                WHERE beampro_user_id = :beampro_user_id
                    AND expired IS NULL
                    AND (
                        expires IS NULL
                        OR expires > :expires
                    )
            ", array(
                ':beampro_user_id' => $beampro_user_id,
                ':expires' => date('Y-m-d H:i:s')
            ));
            
            if(!empty($unexpired_token)) {
                static::$unexpired_tokens[$beampro_user_id] = $unexpired_token;
            }
        }
    }
    
    public static function expireKeys($beampro_user_id, $cache_query_name = NULL) {
        if(!empty($cache_query_name)) {
            $cache_query_name .= '_expire_keys';
        }
    
        db()->query("
            UPDATE beampro_user_tokens
            SET expired = :expired
            WHERE beampro_user_id = :beampro_user_id
                AND expired IS NULL
        ", array(
            ':expired' => date('Y-m-d H:i:s'),
            ':beampro_user_id' => $beampro_user_id
        ), array(), $cache_query_name);
    }

    public static function save($beampro_user_id, DatabaseBeamproUserToken $user_token, $cache_query_name = NULL) {
        $user_token_record = static::getUnexpired($beampro_user_id);
        
        $beampro_user_token_id = NULL;
        
        if(empty($user_token_record)) {        
            $user_token->created = date('Y-m-d H:i:s');
            
            $insert_record = array();
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
                
                $insert_record = $user_token->toArray();
            }
            else {
                $insert_record = $user_token->toArray(false);
            }
            
            static::expireKeys($beampro_user_id, $cache_query_name);
        
            $beampro_user_token_id = db()->insert('beampro_user_tokens', $insert_record, $cache_query_name);
            
            static::addUnexpired($beampro_user_id, $insert_record);
        }
        else {
            $beampro_user_token_id = $user_token_record['beampro_user_token_id'];
        
            $update_record = array();
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
                
                $update_record = $user_token->toArray();
                
                unset($update_record['beampro_user_id']);
                unset($update_record['created']);
                unset($update_record['token']);
            }
            else {
                $update_record = $user_token->toArray(false);
            }
        
            db()->update('beampro_user_tokens', $update_record, array(
                'beampro_user_token_id' => $beampro_user_token_id
            ), array(), $cache_query_name);
        }
        
        return $beampro_user_token_id;
    }
}
