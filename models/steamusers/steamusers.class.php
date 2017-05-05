<?php
namespace Modules\Necrolab\Models\SteamUsers;

use \DateTime;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \RegexIterator;
use \RecursiveRegexIterator;
use \Framework\Modules\Module;
use \Framework\Utilities\File;
use \Modules\Necrolab\Models\Leaderboards\Leaderboards;

class SteamUsers {
    protected static $users = array();
    
    protected static $user_ids = array();
    
    protected static function load($steamid) {}
    
    public static function loadIds() {}
    
    public static function get($steamid) {
        static::load($steamid);
        
        return static::$users[$steamid];
    }
    
    public static function getAllIds() {
        static::loadIds();
        
        return static::$user_ids;
    }
    
    public static function getId($steamid) {
        static::loadIds();
        
        $steam_user_id = NULL;
        
        if(isset(static::$user_ids[$steamid])) {
            $steam_user_id = static::$user_ids[$steamid];
        }
        
        return $steam_user_id;
    }
    
    public static function addId($steamid, $steam_user_id) {
        static::$user_ids[$steamid] = $steam_user_id;
    }
    
    public static function getProfileUrl($steamid) {
        return "http://steamcommunity.com/profiles/{$steamid}";        
    }
    
    public static function getIdFromOpenIdIdentity($openid_identity) {
        return (int)str_replace('http://steamcommunity.com/openid/id/', '', $openid_identity);      
    }
    
    public static function deleteJson(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/steam_user_json/{$date->format('Y-m-d')}";
        
        if(is_dir($snapshot_path)) {
            File::deleteDirectoryRecursive("{$snapshot_path}");
        }
    }
    
    public static function saveJson(DateTime $date, $group_number, $data) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/steam_user_json/{$date->format('Y-m-d')}";
        
        if(!is_dir($snapshot_path)) {
            mkdir($snapshot_path);
        }
    
        file_put_contents("{$snapshot_path}/{$group_number}.json", json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    
    public static function getJson($file_path) {    
        return json_decode(file_get_contents($file_path));
    }
    
    public static function getJsonFiles(DateTime $date) {  
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/steam_user_json/{$date->format('Y-m-d')}";
        
        $json_files = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, '/^.+\.json$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                $json_files[] = current($matched_file);
            }
            
            sort($json_files);
        }
        
        return $json_files;
    }
    
    public static function getFormattedApiRecord($data_row) {
        $processed_data = array();
        
        $processed_data['steamid'] = $data_row['steamid'];
        $processed_data['steam_profile_url'] = $data_row['profileurl'];
        $processed_data['twitch'] = $data_row['twitch_username'];
        $processed_data['twitter'] = $data_row['twitter_username'];
        $processed_data['hitbox'] = $data_row['hitbox_username'];
        $processed_data['nico_nico'] = $data_row['nico_nico_url'];
        $processed_data['website'] = $data_row['website'];
        
        return $processed_data;
    }
}
