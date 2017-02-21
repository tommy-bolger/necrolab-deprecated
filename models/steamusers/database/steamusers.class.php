<?php
namespace Modules\Necrolab\Models\SteamUsers\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\SteamUsers\SteamUsers as BaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards;
use \Modules\Necrolab\Models\ExternalSites\Database\ExternalSites as DatabaseExternalSites;

class SteamUsers
extends BaseSteamUsers {
    public static function load($steamid) {
        if(empty(static::$users[$steamid])) {
            static::$users[$steamid] = db()->getRow("
                SELECT *
                FROM steam_users
                WHERE steamid = :steamid
            ", array(
                ':steamid' => $steamid
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            $user_ids = db()->getMappedColumn("
                SELECT
                    steamid,
                    steam_user_id
                FROM steam_users
            ");
            
            if(!empty($user_ids)) {
                static::$user_ids = $user_ids;
            }
        }
    }
    
    protected static function getBaseResultset() {
        $resultset = new SQL('steam_users');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            ),
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            ),
            array(
                'field' => 'su.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'su.updated',
                'alias' => 'updated'
                
            )
        ));
        
        $resultset->setFromTable('steam_users su');
        
        return $resultset;
    }
    
    public static function getAllResultset() {
        $resultset = static::getBaseResultset();
        
        $resultset->setName('all_steam_users');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            ),
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            ),
            array(
                'field' => 'su.steam_user_id',
                'alias' => 'steam_user_id'
            )
        ));
        
        DatabaseExternalSites::addSiteUserLeftJoins($resultset);
        
        return $resultset;
    }
    
    public static function getAllDisplayResultset() {
        $resultset = static::getAllResultset();
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addSortCriteria('su.personaname', 'ASC');
        
        return $resultset;
    }
    
    public static function getOneDisplayResultset($steamid) {
        $resultset = static::getAllResultset();
        
        $resultset->addSelectField('profileurl');
        
        $resultset->addFilterCriteria("steamid = :steamid", array(
            ':steamid' => $steamid
        ));
        
        return $resultset;
    }
    
    public static function getOutdatedIds() {
        $thirty_days_ago = new DateTime('-30 day');
    
        return db()->getMappedColumn("
            SELECT
                steam_user_id,
                steamid
            FROM steam_users
            WHERE updated < :updated
        ", array(
            ':updated' => $thirty_days_ago->format('Y-m-d H:i:s')
        ));
    }
    
    public static function getRecordModel(array $properties) {
        $record_model = new DatabaseSteamUser();
        
        $record_model->setPropertiesFromArray($properties);
        
        return $record_model;
    }
    
    public static function save(DatabaseSteamUser $steam_user, $cache_query_name = NULL) {
        $steam_user_id = static::getId($steam_user->steamid);
        
        if(empty($steam_user_id)) {
            $updated = new DateTime('-31 day');
        
            $steam_user->updated = $updated->format('Y-m-d H:i:s');
            
            $user_record = array();
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_insert';
                
                $user_record = $steam_user->toArray();
            }
            else {
                $user_record = $steam_user->toArray(false);
            }
        
            $steam_user_id = db()->insert('steam_users', $user_record, $cache_query_name);
            
            static::addId($steam_user->steamid, $steam_user_id);
        }
        else {            
            if(isset($user_record['steamid'])) {
                unset($user_record['steamid']);
            }
            
            $user_record = array();
            
            if(!empty($cache_query_name)) {
                $cache_query_name .= '_update';
                
                $user_record = $steam_user->toArray();
            }
            else {
                $user_record = $steam_user->toArray(false);
            }
        
            db()->update('steam_users', $user_record, array(
                'steam_user_id' => $steam_user_id
            ), array(), $cache_query_name);
        }
        
        return $steam_user_id;
    }
}
