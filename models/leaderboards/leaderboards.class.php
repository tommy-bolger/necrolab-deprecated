<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \DateTime;
use \Exception;
use \SimpleXMLElement;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \RegexIterator;
use \RecursiveRegexIterator;
use \Framework\Data\XMLWrite;
use \Framework\Utilities\File;
use \Framework\Modules\Module;
use \Modules\Necrolab\Models\Necrolab;

class Leaderboards
extends Necrolab {
    protected static $leaderboards = array();
    
    public static function loadAll() {}

    public static function get($lbid) {
        static::loadAll();
        
        $leaderboard_record = array();
        
        if(!empty(static::$leaderboards[$lbid])) {
            $leaderboard_record = static::$leaderboards[$lbid];
        }
        
        return $leaderboard_record;
    }
    
    public static function getSteamXml($leaderboards_url) {    
        $request = curl_init($leaderboards_url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        
        $leaderboards_xml = curl_exec($request);
        
        $http_response_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
        
        $request_error = curl_error($request);
        
        if(!empty($request_error)) {
            $error_code = curl_errno($request);
        
            if(!empty($error_code)) {
                $error_message = curl_strerror($error_code);
                
                throw new Exception("HTTP request to Steam leaderboards encountered an error with number '{$error_code}' and message '{$error_message}'.");
            }
            else {
                throw new Exception('Response from Steam leaderboards returned with an unknown error.');
            }
        }
        
        return $leaderboards_xml;
    }
    
    public static function getParsedXml($unparsed_xml) {
        //Strip any non UTF-8 character from the document that causes the XML to break.
        $leaderboards_xml = preg_replace('/[^[:print:]]/', '', $unparsed_xml);
        
        unset($unparsed_xml);
        
        $leaderboards = NULL;
        
        if(!empty($leaderboards_xml)) {
            $leaderboards = XMLWrite::convertXmlToObject(new SimpleXMLElement($leaderboards_xml));
        }
        
        unset($leaderboards_xml);
        
        return $leaderboards;
    }
    
    public static function deleteXml(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/{$date->format('Y-m-d')}";
        
        if(is_dir($snapshot_path)) {
            File::deleteDirectoryRecursive("{$snapshot_path}");
        }
    }
    
    public static function saveXml(DateTime $date, $xml) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/{$date->format('Y-m-d')}";
        
        if(!is_dir($snapshot_path)) {
            mkdir($snapshot_path);
        }
    
        file_put_contents("{$snapshot_path}/leaderboards.xml.gz", gzencode($xml, 9));
    }
    
    /*public static function getOldXml($file_path) {    
        return gzuncompress(file_get_contents($file_path));
    }*/
    
    public static function getXml($file_path) {    
        return gzdecode(file_get_contents($file_path));
    }
    
    public static function getXmlFiles(DateTime $date) {  
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/{$date->format('Y-m-d')}";
        
        $xml_file_groups = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, '/^.+\.gz$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                if(strpos($matched_file[0], 'leaderboards.xml.gz') !== false) {
                    $xml_file_groups['leaderboards_xml'] = $matched_file[0];
                }
                else {
                    $file_name = $matched_file[0];
                    $file_name_split = explode('/', $matched_file[0]);
                    
                    $xml_file_name = array_pop($file_name_split);
                    $xml_file_name_split = explode('_', $xml_file_name);
                    
                    $page_number = array_pop($xml_file_name_split);
                    $page_number = (int)str_replace('.xml.gz', '', $page_number);
                    
                    $lbid = array_pop($file_name_split);
                    
                    if(empty($xml_file_groups[$lbid])) {
                        $xml_file_groups[$lbid] = array();
                    }
                        
                    $xml_file_groups[$lbid][$page_number] = $matched_file[0];
                }
            }
            
            if(!empty($xml_file_groups)) {
                foreach($xml_file_groups as $lbid => &$xml_files) {
                    if($lbid != 'leaderboards_xml') {
                        ksort($xml_files);
                    }
                }
            }
        }
        
        return $xml_file_groups;
    }
    
    public static function deleteS3Xml(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/s3_queue/{$date->format('Y-m-d')}";
        
        if(is_dir($snapshot_path)) {
            File::deleteDirectoryRecursive("{$snapshot_path}");
        }
    }
    
    public static function saveS3Xml(DateTime $date, $xml) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/s3_queue/{$date->format('Y-m-d')}";
        
        if(!is_dir($snapshot_path)) {
            mkdir($snapshot_path);
        }
    
        file_put_contents("{$snapshot_path}/leaderboards.xml", $xml);
    }
    
    public static function compressS3Xml(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
    
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_parent_path = "{$installation_path}/leaderboard_xml/s3_queue";
        $snapshot_path = "{$snapshot_parent_path}/{$date_formatted}";
        
        $zip_snapshot_path = "{$snapshot_path}.zip";
    
        /* 
            Since this will only run on the backend this would be simplest way to compress an entire folder.
            TODO: Implement a method in the File utility to recursively compress all files in a file using ZipArchive.
        */
        exec("cd {$snapshot_parent_path} ; zip -r {$date_formatted}.zip {$date_formatted}");
        
        static::deleteS3Xml($date);
    
        return $zip_snapshot_path;
    }
    
    public static function deleteS3ZippedXml(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/s3_queue/{$date->format('Y-m-d')}.zip";
    
        unlink($snapshot_path);
    }
    
    public static function getFormattedApiRecord($data_row) {
        return array(
            'lbid' => (int)$data_row['lbid'],
            'name' => $data_row['leaderboard_name'],
            'display_name' => $data_row['leaderboard_display_name'],
            'entries_url' => $data_row['url'],
            'character' => $data_row['character_name'],
            'character_number' => $data_row['character_number'],
            'is_daily' => (int)$data_row['is_daily'],
            'daily_date' => $data_row['daily_date'],
            'is_score_run' => (int)$data_row['is_score_run'],
            'is_speedrun' => (int)$data_row['is_speedrun'],
            'is_deathless' => (int)$data_row['is_deathless'],
            'is_seeded' => (int)$data_row['is_seeded'],
            'is_co_op' => (int)$data_row['is_co_op'],
            'is_custom' => (int)$data_row['is_custom'],
            'is_power_ranking' => (int)$data_row['is_power_ranking'],
            'is_daily_ranking' => (int)$data_row['is_daily_ranking']
        );
    }
}