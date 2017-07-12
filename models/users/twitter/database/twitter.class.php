<?php
namespace Modules\Necrolab\Models\Users\Twitter\Database;

use \DateTime;
use \Modules\Necrolab\Models\Users\Twitter\Twitter as BaseTwitter;
use \Modules\Necrolab\Models\Users\Twitter\Database\RecordModels\TwitterUser as DatabaseTwitterUser;

class Twitter
extends BaseTwitter {
    public static function load($twitter_id) {
        if(empty(static::$users[$twitter_id])) {
            static::$users[$twitter_id] = db()->getRow("
                SELECT *
                FROM twitter_users
                WHERE twitter_id = :twitter_id
            ", array(
                ':twitter_id' => $twitter_id
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            $user_ids = db()->getMappedColumn("
                SELECT
                    twitter_id,
                    twitter_user_id
                FROM twitter_users
            ");
            
            if(!empty($user_ids)) {
                static::$user_ids = $user_ids;
            }
        }
    }
    
    public static function save(DatabaseTwitterUser $twitter_user, $cache_query_name = NULL) {
        $twitter_user_id = static::getId($twitter_user->twitter_id);
        
        if(empty($twitter_user_id)) {
            $updated = new DateTime('-31 day');
        
            $twitter_user->updated = $updated->format('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
        
            $twitter_user_id = db()->insert('twitter_users', $twitter_user->toArray(), $cache_query_name);
            
            static::addId($twitter_user->twitter_id, $twitter_user_id);
        }
        else {
            $twitter_user->updated = date('Y-m-d H:i:s');       
            $user_record = $twitter_user->toArray();
            
            if(isset($user_record['twitter_id'])) {
                unset($user_record['twitter_id']);
            }
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('twitter_users', $user_record, array(
                'twitter_user_id' => $twitter_user_id
            ), array(), $cache_query_name);
        }
        
        return $twitter_user_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE twitter_users;");
    }
    
    public static function addSiteUserFilter($resultset) {    
        $resultset->addFilterCriteria("su.twitter_user_id IS NOT NULL");
        
        $resultset->addSelectFields(array(
            array(
                'field' => "(twu.nickname || '@' || twu.name)",
                'alias' => 'personaname'
            ),
            array(
                'field' => 'twu.twitter_id',
                'alias' => 'twitter_id'
            )
        ));
        
        return $resultset;
    }
}
