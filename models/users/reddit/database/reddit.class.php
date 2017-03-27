<?php
namespace Modules\Necrolab\Models\Users\Reddit\Database;

use \DateTime;
use \Modules\Necrolab\Models\Users\Reddit\Reddit as BaseReddit;
use \Modules\Necrolab\Models\Users\Reddit\Database\RecordModels\RedditUser as DatabaseRedditUser;

class Reddit
extends BaseReddit {
    public static function load($reddit_id) {
        if(empty(static::$users[$reddit_id])) {
            static::$users[$reddit_id] = db()->getRow("
                SELECT *
                FROM reddit_users
                WHERE reddit_id = :reddit_id
            ", array(
                ':reddit_id' => $reddit_id
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            $user_ids = db()->getMappedColumn("
                SELECT
                    reddit_id,
                    reddit_user_id
                FROM reddit_users
            ");
            
            if(!empty($user_ids)) {
                static::$user_ids = $user_ids;
            }
        }
    }
    
    public static function save(DatabaseRedditUser $reddit_user, $cache_query_name = NULL) {
        $reddit_user_id = static::getId($reddit_user->reddit_id);
        
        if(empty($reddit_user_id)) {
            $updated = new DateTime('-31 day');
        
            $reddit_user->updated = $updated->format('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
        
            $reddit_user_id = db()->insert('reddit_users', $reddit_user->toArray(), $cache_query_name);
            
            static::addId($reddit_user->reddit_id, $reddit_user_id);
        }
        else {
            $reddit_user->updated = date('Y-m-d H:i:s');       
            $user_record = $reddit_user->toArray();
            
            if(isset($user_record['reddit_id'])) {
                unset($user_record['reddit_id']);
            }
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('reddit_users', $user_record, array(
                'reddit_user_id' => $reddit_user_id
            ), array(), $cache_query_name);
        }
        
        return $reddit_user_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE reddit_users;");
    }
    
    public static function addSiteUserJoin($resultset) {    
        $resultset->addJoinCriteria("reddit_users site_user ON site_user.reddit_user_id = su.reddit_user_id");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'site_user.username',
                'alias' => 'personaname'
            ),
            array(
                'field' => 'site_user.reddit_id',
                'alias' => 'reddit_id'
            )
        ));
        
        return $resultset;
    }
}
