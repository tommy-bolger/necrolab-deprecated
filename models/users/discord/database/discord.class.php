<?php
namespace Modules\Necrolab\Models\Users\Discord\Database;

use \DateTime;
use \Modules\Necrolab\Models\Users\Discord\Discord as BaseDiscord;
use \Modules\Necrolab\Models\Users\Discord\Database\RecordModels\DiscordUser as DatabaseDiscordUser;

class Discord
extends BaseDiscord {
    public static function load($discord_id) {
        if(empty(static::$users[$discord_id])) {
            static::$users[$discord_id] = db()->getRow("
                SELECT *
                FROM discord_users
                WHERE discord_id = :discord_id
            ", array(
                ':discord_id' => $discord_id
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            $user_ids = db()->getMappedColumn("
                SELECT
                    discord_id,
                    discord_user_id
                FROM discord_users
            ");
            
            if(!empty($user_ids)) {
                static::$user_ids = $user_ids;
            }
        }
    }
    
    public static function save(DatabaseDiscordUser $discord_user, $cache_query_name = NULL) {
        $discord_user_id = static::getId($discord_user->discord_id);
        
        if(empty($discord_user_id)) {
            $updated = new DateTime('-31 day');
        
            $discord_user->updated = $updated->format('Y-m-d H:i:s');
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
            }
        
            $discord_user_id = db()->insert('discord_users', $discord_user->toArray(), $cache_query_name);
            
            static::addId($discord_user->discord_id, $discord_user_id);
        }
        else {
            $discord_user->updated = date('Y-m-d H:i:s');       
            $user_record = $discord_user->toArray();
            
            if(isset($user_record['discord_id'])) {
                unset($user_record['discord_id']);
            }
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
            }
        
            db()->update('discord_users', $user_record, array(
                'discord_user_id' => $discord_user_id
            ), array(), $cache_query_name);
        }
        
        return $discord_user_id;
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE discord_users;");
    }
    
    public static function addSiteUserFilter($resultset) {    
        $resultset->addFilterCriteria("su.discord_user_id IS NOT NULL");
        
        $resultset->addSelectFields(array(
            array(
                'field' => "(du.username || '#' || du.discriminator)",
                'alias' => 'personaname'
            ),
            array(
                'field' => 'du.discord_id',
                'alias' => 'discord_id'
            )
        ));
        
        return $resultset;
    }
}
