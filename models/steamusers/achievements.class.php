<?php
namespace Modules\Necrolab\Models\SteamUsers;

use \DateTime;
use \Exception;
use \SimpleXMLElement;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \RegexIterator;
use \RecursiveRegexIterator;
use \Framework\Utilities\File;
use \Framework\Data\XMLWrite;
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
    
    public static function getSteamXml($steamid, $app_id) {    
        $request = curl_init("http://steamcommunity.com/profiles/{$steamid}/stats/{$app_id}/achievements/?xml=1");
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        
        $steam_user_achievements_xml = curl_exec($request);
        
        $http_response_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
        
        $request_error = curl_error($request);
        
        if(!empty($request_error)) {
            $error_code = curl_errno($request);
        
            if(!empty($error_code)) {
                $error_message = curl_strerror($error_code);
                
                throw new Exception("HTTP request to Steam user achievements encountered an error with number '{$error_code}' and message '{$error_message}'.");
            }
            else {
                throw new Exception('Response from Steam  user achievements returned with an unknown error.');
            }
        }
        
        if($http_response_code != 200) {
            $steam_user_achievements_xml = NULL;
        }
        
        if(strpos($steam_user_achievements_xml, '<!DOCTYPE html>') !== false) {
            $steam_user_achievements_xml = NULL;
        }
        
        return $steam_user_achievements_xml;
    }
    
    public static function getParsedXml($unparsed_xml) {    
        $parsed_xml = NULL;
        
        if(
            !empty($unparsed_xml) && 
            strpos($unparsed_xml, '<!DOCTYPE html>') === false && 
            strpos($unparsed_xml, '<?xml version="1.0"') !== false
        ) {
            $steam_user_achievements_xml = preg_replace('/[^[:print:]]/', '', $unparsed_xml);
        
            if(!empty($steam_user_achievements_xml)) {
                $parsed_xml = XMLWrite::convertXmlToObject(new SimpleXMLElement($steam_user_achievements_xml));
            }
            
            unset($steam_user_achievements_xml);
        }
        
        return $parsed_xml;
    }
    
    public static function getXmlPath() {        
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        
        return "{$installation_path}/assets/files/achievement_xml";
    }
    
    public static function saveXml($steam_user_id, $xml) {
        $snapshot_path = static::getXmlPath();
        
        if(!is_dir($snapshot_path)) {
            mkdir($snapshot_path);
        }
    
        file_put_contents("{$snapshot_path}/{$steam_user_id}.xml", $xml);
    }
    
    public static function getXml($file_path) {    
        return file_get_contents($file_path);
    }
    
    public static function deleteXml($file_path) {        
        unlink($file_path);
    }
    
    public static function getXmlFiles() {  
        $snapshot_path = static::getXmlPath();
        
        $xml_files = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, '/^.+\.xml$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                $matched_file_path = current($matched_file);
            
                $steam_user_id = str_replace(array(
                    $snapshot_path,
                    '.xml',
                    '/'
                ), '', $matched_file_path);

                $xml_files[(int)$steam_user_id] = $matched_file_path;
            }
        }
        
        return $xml_files;
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