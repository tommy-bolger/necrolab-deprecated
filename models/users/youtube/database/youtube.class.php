<?php
namespace Modules\Necrolab\Models\Users\Youtube\Database;

use \DateTime;
use \Modules\Necrolab\Models\Users\Youtube\Youtube as BaseYoutube;
use \Modules\Necrolab\Models\Users\Youtube\Database\RecordModels\YoutubeUser as DatabaseYoutubeUser;

class Youtube
extends BaseYoutube {
    public static function load($youtube_id) {
        if(empty(static::$users[$youtube_id])) {
            static::$users[$youtube_id] = db()->getRow("
                SELECT *
                FROM youtube_users
                WHERE youtube_id = :youtube_id
            ", array(
                ':youtube_id' => $youtube_id
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            $user_ids = db()->getMappedColumn("
                SELECT
                    youtube_id,
                    youtube_user_id
                FROM youtube_users
            ");
            
            if(!empty($user_ids)) {
                static::$user_ids = $user_ids;
            }
        }
    }
    
    public static function save(DatabaseYoutubeUser $youtube_user, $cache_query_name = NULL) {
        $youtube_user_id = static::getId($youtube_user->youtube_id);
        
        if(empty($youtube_user_id)) {
            $updated = new DateTime('-31 day');
        
            $youtube_user->updated = $updated->format('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
        
            $youtube_user_id = db()->insert('youtube_users', $youtube_user->toArray(), $cache_query_name);
            
            static::addId($youtube_user->youtube_id, $youtube_user_id);
        }
        else {
            $youtube_user->updated = date('Y-m-d H:i:s');       
            $user_record = $youtube_user->toArray();
            
            if(isset($user_record['youtube_id'])) {
                unset($user_record['youtube_id']);
            }
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('youtube_users', $user_record, array(
                'youtube_user_id' => $youtube_user_id
            ), array(), $cache_query_name);
        }
        
        return $youtube_user_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE youtube_users;");
    }
    
    public static function addSiteUserFilter($resultset) {    
        $resultset->addFilterCriteria("su.youtube_user_id IS NOT NULL");
        
        $resultset->addSelectFields(array(
            array(
                'field' => "yu.youtube_id",
                'alias' => 'personaname'
            ),
            array(
                'field' => 'yu.youtube_id',
                'alias' => 'youtube_id'
            )
        ));
        
        return $resultset;
    }
}
