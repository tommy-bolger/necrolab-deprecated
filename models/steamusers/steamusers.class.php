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
    
    protected static function getUngroupedLeaderboards($category_name) {}
    
    public static function getUserCategoryLeaderboards($steamid, $category_name, array $ungrouped_leaderboards, array $leaderboard_entries) {
        assert('!empty($leaderboard_entries)');
    
        $empty_leaderboard_row = array(
            'name' => '',
            'cadence' => NULL,
            'bard' => NULL,
            'monk' => NULL,
            'aria' => NULL,
            'bolt' => NULL,
            'dove' => NULL,
            'eli' => NULL,
            'melody' => NULL,
            'dorian' => NULL,
            'coda' => NULL,
            'all' => NULL,
            'story' => NULL
        );
        
        $ungrouped_leaderboards = static::getUngroupedLeaderboards($category_name);
            
        $grouped_leaderboards = Leaderboards::getGroupedLeaderboards($category_name, $ungrouped_leaderboards);
    
        $category_entries = array();
    
        if(!empty($grouped_leaderboards)) {
            foreach($grouped_leaderboards as $leaderboard_group_name => $leaderboard_group) {
                $leaderboard_characters = $leaderboard_group['characters'];
                
                $rank_row = array();
            
                foreach($leaderboard_characters as $character_name => $lbid) {
                    foreach($leaderboard_entries as $leaderboard_entry) {
                        if($leaderboard_entry['lbid'] == $lbid) {
                            $rank_row['name'] = $leaderboard_group['name'];
                            $rank_row[$character_name] = $leaderboard_entry['rank'];
                        }
                    }
                }
                
                if(!empty($rank_row)) {
                    $group_rank_row = array_merge($empty_leaderboard_row, $rank_row);
                    
                    $category_entries[] = $group_rank_row;
                }
            }
        }
        
        return $category_entries;
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
    
        file_put_contents("{$snapshot_path}/{$group_number}.json.gz", gzcompress(json_encode($data), 9));
    }
    
    public static function getJson($file_path) {    
        return json_decode(gzuncompress(file_get_contents($file_path)));
    }
    
    public static function getJsonFiles(DateTime $date) {  
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/steam_user_json/{$date->format('Y-m-d')}";
        
        $json_files = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, '/^.+\.gz$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                $json_files[] = current($matched_file);
            }
            
            sort($json_files);
        }
        
        return $json_files;
    }
}
