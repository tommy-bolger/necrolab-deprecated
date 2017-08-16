<?php
namespace Modules\Necrolab\Models\SteamUsers;

use \DateTime;
use \Exception;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \RegexIterator;
use \RecursiveRegexIterator;
use \Framework\Utilities\File;
use \Framework\Modules\Module;
use \Modules\Necrolab\Models\Necrolab;

class Achievements
extends Necrolab {
    protected static $achievements_by_user = array();
    
    protected static $ids = array();

    protected static function loadUser($steam_user_id) {}
    
    protected static function loadIds() {}
    
    public static function getForUser($steam_user_id) {
        static::loadUser($steam_user_id);
        
        return static::$achievements_by_user[$steam_user_id];
    }
    
    public static function recordExists($steam_user_id, $achievement_id) {
        static::loadIds();
        
        $exists = false;
        
        if(isset(static::$ids[$steam_user_id])) {
            if(isset(static::$ids[$steam_user_id][$achievement_id])) {
                $exists = true;
            }
        }
        
        return $exists;
    }
    
    public static function unsetUser($steam_user_id) {
        if(isset(static::$achievements_by_user[$steam_user_id])) {
            unset(static::$achievements_by_user[$steam_user_id]);
        }
    }
    
    public static function getJsonPath() {        
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        
        return "{$installation_path}/assets/files/achievement_json";
    }
    
    public static function saveJson($steam_user_id, $json) {
        $snapshot_path = static::getJsonPath();
        
        if(!is_dir($snapshot_path)) {
            mkdir($snapshot_path);
        }
    
        file_put_contents("{$snapshot_path}/{$steam_user_id}.json", $json);
    }
    
    public static function getJson($file_path) {    
        return file_get_contents($file_path);
    }
    
    public static function deleteJson($file_path) {        
        unlink($file_path);
    }
    
    public static function getJsonFiles() {  
        $snapshot_path = static::getJsonPath();
        
        $json_files = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, '/^.+\.json$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                $matched_file_path = current($matched_file);
            
                $steam_user_id = str_replace(array(
                    $snapshot_path,
                    '.json',
                    '/'
                ), '', $matched_file_path);

                $json_files[(int)$steam_user_id] = $matched_file_path;
            }
        }
        
        return $json_files;
    }
    
    public static function getFormattedApiRecord($data_row) {    
        $achieved_date = $data_row['achieved'];
        
        $achieved = 0;
        $icon_url = $data_row['icon_gray_url'];
        
        if(!empty($achieved_date)) {
            $achieved = 1;
            $icon_url = $data_row['icon_url'];
        }
    
        return array(
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name'],
            'description' => $data_row['description'],
            'achieved' => $achieved,
            'achieved_date' => $achieved_date,
            'icon_url' => $icon_url
        );
    }
}