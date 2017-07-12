<?php
namespace Modules\Necrolab\Models\Users\Twitch\Database;

use \DateTime;
use \Modules\Necrolab\Models\Users\Twitch\Twitch as BaseTwitch;
use \Modules\Necrolab\Models\Users\Twitch\Database\RecordModels\TwitchUser as DatabaseTwitchUser;

class Twitch
extends BaseTwitch {
    public static function load($twitch_id) {
        if(empty(static::$users[$twitch_id])) {
            static::$users[$twitch_id] = db()->getRow("
                SELECT *
                FROM twitch_users
                WHERE twitch_id = :twitch_id
            ", array(
                ':twitch_id' => $twitch_id
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            $user_ids = db()->getMappedColumn("
                SELECT
                    twitch_id,
                    twitch_user_id
                FROM twitch_users
            ");
            
            if(!empty($user_ids)) {
                static::$user_ids = $user_ids;
            }
        }
    }
    
    public static function save(DatabaseTwitchUser $twitch_user, $cache_query_name = NULL) {
        $twitch_user_id = static::getId($twitch_user->twitch_id);
        
        if(empty($twitch_user_id)) {
            $updated = new DateTime('-31 day');
        
            $twitch_user->updated = $updated->format('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
        
            $twitch_user_id = db()->insert('twitch_users', $twitch_user->toArray(), $cache_query_name);
            
            static::addId($twitch_user->twitch_id, $twitch_user_id);
        }
        else {
            $twitch_user->updated = date('Y-m-d H:i:s');       
            $user_record = $twitch_user->toArray();
            
            unset($user_record['twitch_id']);
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('twitch_users', $user_record, array(
                'twitch_user_id' => $twitch_user_id
            ), array(), $cache_query_name);
        }
        
        return $twitch_user_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE twitch_users;");
    }
    
    public static function addSiteUserFilter($resultset) {    
        $resultset->addFilterCriteria("su.twitch_user_id IS NOT NULL");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'tu.username',
                'alias' => 'personaname'
            ),
            array(
                'field' => 'tu.twitch_id',
                'alias' => 'twitch_id'
            )
        ));
        
        return $resultset;
    }
}
