<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \DateTime;
use \DateInterval;
use \Exception;
use \SimpleXMLElement;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \RegexIterator;
use \RecursiveRegexIterator;
use \Framework\Data\XMLWrite;
use \Framework\Utilities\File;
use \Framework\Utilities\Encryption;
use \Framework\Modules\Module;
use \Modules\Necrolab\Models\Releases;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\Characters;
use \Modules\Necrolab\Models\Necrolab;

class Leaderboards
extends Necrolab {
    protected static $leaderboards = array();
    
    protected static $ids = array();
    
    public static function load($leaderboard_id) {}

    public static function get($leaderboard_id) {
        static::load($leaderboard_id);
        
        $leaderboard_record = array();
        
        if(!empty(static::$leaderboards[$leaderboard_id])) {
            $leaderboard_record = static::$leaderboards[$leaderboard_id];
        }
        
        return $leaderboard_record;
    }
    
    public static function getId($lbid) {
        static::loadIds();
        
        $leaderboard_id = NULL;
        
        if(!empty(static::$ids[$lbid])) {
            $leaderboard_id = static::$ids[$lbid];
        }
        
        return $leaderboard_id;
    }
    
    public static function getSteamXml($leaderboards_url) {    
        $request = curl_init($leaderboards_url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        
        $leaderboards_xml = curl_exec($request);
        
        $http_response_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
        
        $request_error = curl_error($request);
        
        if($http_response_code != 200 || !empty($request_error)) {
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
    
    public static function getXmlPath(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
    
        return "{$installation_path}/leaderboard_xml/{$date->format('Y-m-d')}";
    }
    
    public static function deleteXml(DateTime $date) {
        $snapshot_path = static::getXmlPath($date);
        
        if(is_dir($snapshot_path)) {
            File::deleteDirectoryRecursive($snapshot_path);
        }
    }
    
    public static function getOldXml($file_path) {    
        return gzdecode(file_get_contents($file_path));
    }
    
    public static function getXml($file_path) {    
        return file_get_contents($file_path);
    }
    
    public static function getXmlFiles(DateTime $date, $temp_directory = false) {  
        $snapshot_path = NULL;
        $search_extension = NULL;
        $full_extension = 'xml';
        
        if(empty($temp_directory)) {
            $snapshot_path = static::getXmlPath($date);
            
            $search_extension = 'gz';
            $full_extension .= '.gz';
        }
        else {
            $snapshot_path = static::getXmlTempPath($date);
            
            $search_extension = 'xml';
        }
        
        $xml_file_groups = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, "/^.+\.{$search_extension}$/i", RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                if(strpos($matched_file[0], "leaderboards.{$full_extension}") !== false) {
                    $xml_file_groups['leaderboards_xml'] = $matched_file[0];
                }
                else {
                    $file_name = $matched_file[0];
                    $file_name_split = explode('/', $matched_file[0]);
                    
                    $xml_file_name = array_pop($file_name_split);
                    $xml_file_name_split = explode('_', $xml_file_name);
                    
                    $page_number = array_pop($xml_file_name_split);
                    $page_number = (int)str_replace(".{$full_extension}", '', $page_number);
                    
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
    
    public static function getXmlTempPath(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
    
        return "{$installation_path}/leaderboard_xml/temp/{$date->format('Y-m-d')}";
    }
    
    public static function saveTempXml(DateTime $date, $xml) {
        $snapshot_path = static::getXmlTempPath($date);
        
        if(!is_dir($snapshot_path)) {
            mkdir($snapshot_path);
        }
    
        file_put_contents("{$snapshot_path}/leaderboards.xml", $xml);
    }
    
    public static function deleteTempXml(DateTime $date) {
        $snapshot_path = static::getXmlTempPath($date);
        
        if(is_dir($snapshot_path)) {
            File::deleteDirectoryRecursive($snapshot_path);
        }
    }
    
    public static function compressTempToSavedXml(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
    
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $temp_parent_path = "{$installation_path}/leaderboard_xml/temp";
        
        $zip_snapshot_path = static::getXmlPath($date) . ".zip";
    
        /* 
            Since this will only run on the backend this would be simplest way to compress an entire folder.
            TODO: Implement a method in the File utility to recursively compress all files in a file using ZipArchive.
        */
        exec("cd {$temp_parent_path} ; zip -r {$date_formatted}.zip {$date_formatted}; rm -rf {$zip_snapshot_path}; mv {$date_formatted}.zip {$zip_snapshot_path}");
    
        return $zip_snapshot_path;
    }
    
    public static function decompressToTempXml(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
    
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $temp_parent_path = "{$installation_path}/leaderboard_xml/temp";
        
        $zip_snapshot_path = static::getXmlPath($date) . ".zip";
    
        /* 
            Since this will only run on the backend this would be simplest way to decompress an entire folder.
            TODO: Implement a method in the File utility to recursively decompress all files in a file using ZipArchive.
        */
        exec("unzip {$zip_snapshot_path} -d {$temp_parent_path}");
    }
    
    public static function copyZippedXmlToS3(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
    
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $s3_parent_path = "{$installation_path}/leaderboard_xml/s3_queue";
        
        $zip_snapshot_path = static::getXmlPath($date) . ".zip";
    
        /* 
            Since this will only run on the backend this would be simplest way to decompress an entire folder.
            TODO: Implement a method in the File utility to copy this file over.
        */
        exec("cp {$zip_snapshot_path} {$s3_parent_path}");
        
        return "{$s3_parent_path}/{$date_formatted}.zip";
    }
    
    public static function deleteS3ZippedXml(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/s3_queue/{$date->format('Y-m-d')}.zip";
    
        if(is_file($snapshot_path)) {
            unlink($snapshot_path);
        }
    }
    
    public static function getXmlUrls() {
        $start_date = new DateTime('2017-01-01');
        $end_date = new DateTime(date('Y-m-d'));
        
        $current_date = clone $start_date;
        
        $xml_urls = array();
        
        while($current_date <= $end_date) {
            $xml_urls[] = "https://necrolab.s3.amazonaws.com/leaderboard_xml/{$current_date->format('Y-m-d')}.zip";
        
            $current_date->add(new DateInterval('P1D'));
        }
        
        return $xml_urls;
    }
    
    public static function getXmlSaveQueueName() {
        return "save_xml";
    }
    
    public static function addToXmlSaveQueue(DateTime $date) {
        static::addDateToQueue(static::getXmlSaveQueueName(), $date);
    }
    
    public static function getXmlUploadQueueName() {
        return "upload_xml";
    }
    
    public static function addToXmlUploadQueue(DateTime $date) {        
        static::addDateToQueue(static::getXmlUploadQueueName(), $date);
    }
    
    public static function isValidLeaderboard($release_name, $mode_name, $character_name, $type, $seeded, $is_co_op, $is_custom) {
        $is_valid = true;
        
        if($release_name == 'original' && $mode_name != 'normal') {
            $is_valid = false;
        }
        
        if($type == 'deathless' && !empty($seeded)) {
            $is_valid = false;
        }
        
        if($type == 'deathless' && $mode_name != 'normal') {
            $is_valid = false;
        }
        
        if($release_name == 'original' && !Characters::isOriginalCharacter($character_name)) {
            $is_valid = false;
        }
        
        if($release_name == 'amplified_dlc' && !Characters::isAmplifiedDlcCharacter($character_name)) {
            $is_valid = false;
        }        
        
        if($mode_name != 'normal' && !Characters::isModeCharacter($character_name)) {
            $is_valid = false;
        }
        
        if($type == 'deathless' && !Characters::isDeathlessCharacter($character_name)) {
            $is_valid = false;
        }
        
        if(!empty($seeded) && !Characters::isSeededCharacter($character_name)) {
            $is_valid = false;
        }
        
        if(!empty($is_co_op) && !Characters::isCoOpCharacter($character_name)) {
            $is_valid = false;
        }
        
        return $is_valid;
    }
    
    public static function generateName($release, $mode, $character, $type, $seeded, $is_co_op, $is_custom) {
        $leaderboard_name = '';
        
        $release_name = $release['name'];
        $mode_name = $mode['name'];
        $character_name = $character['name'];
                                        
        if(static::isValidLeaderboard($release_name, $mode_name, $character_name, $type, $seeded, $is_co_op, $is_custom)) {
            $character_display_name = $character['display_name'];
            
            if($character_name == 'dove') {
                $character_display_name = strtoupper($character_display_name);
            }
        
            $leaderboard_name_segments = array();
            
            if($release_name == 'amplified_dlc') {
                $leaderboard_name_segments[] = 'DLC';
            }
        
            switch($type) {
                case 'score':
                case 'deathless':
                    $leaderboard_name_segments[] = 'HARDCORE';
                    
                    if(!empty($seeded)) {
                        $leaderboard_name_segments[] = 'SEEDED';
                    }
                    break;
                case 'speed':
                    if(!empty($seeded)) {
                        $leaderboard_name_segments[] = 'SEEDED';
                    }
                
                    $leaderboard_name_segments[] = 'SPEEDRUN';
                    break;
            }
            
            if($character_name != 'cadence') {
                $leaderboard_name_segments[] = $character_display_name;
            }
            
            if(!empty($is_co_op)) {
                $leaderboard_name_segments[] = 'CO-OP';
            }
            
            if($type == 'deathless') {
                $leaderboard_name_segments[] = 'DEATHLESS';
            }
            elseif($mode_name != 'normal') {
                $mode_segment = '';
            
                if($mode_name == 'no_return') {
                    $mode_segment = 'NO RETURN';
                }
                else {
                    $mode_segment = strtoupper($mode_name);
                }                
            
                $leaderboard_name_segments[] = $mode_segment;
            }
            
            if(!empty($is_custom)) {
                $leaderboard_name_segments[] = 'CUSTOM MUSIC';
            }
            
            $leaderboard_name = implode(' ', $leaderboard_name_segments) . '_PROD';
        }
        
        return $leaderboard_name;
    }
    
    public static function generateAllNames(DateTime $date) {
        $characters = Characters::getActive();
        
        $releases = Releases::getByDate($date);
        
        $modes = Modes::getAll();
        
        $leaderboard_types = array(
            'score',
            'speed',
            'deathless'
        );
        
        $seeded_types = array(
            0, 
            1
        );
        
        $co_op_types = array(
            0,
            1
        );
        
        $custom_types = array(
            0,
            1
        );
        
        $leaderboard_names = array();

        if(!empty($releases)) {
            foreach($releases as $release) {
                if(!empty($modes)) {
                    foreach($modes as $mode) {
                        if(!empty($characters)) {
                            foreach($characters as $character) {
                                if(!empty($leaderboard_types)) {
                                    foreach($leaderboard_types as $leaderboard_type) {
                                        foreach($seeded_types as $seeded) {
                                            foreach($co_op_types as $is_co_op) {
                                                foreach($custom_types as $is_custom) {
                                                    $leaderboard_name = Leaderboards::generateName($release, $mode, $character, $leaderboard_type, $seeded, $is_co_op, $is_custom);
                                                    
                                                    if(!empty($leaderboard_name)) {
                                                        $leaderboard_names[] = $leaderboard_name;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $leaderboard_names;
    }
    
    public static function getLeaderboardNameChunks(array $leaderboard_names) {
        $leaderboard_name_chunks = array();
        
        if(!empty($leaderboard_names)) {
            $leaderboard_name_chunks = array_chunk($leaderboard_names, 100);
        }
        
        return $leaderboard_name_chunks;
    }
    
    public static function generateDailyNames(DateTime $date) {        
        $releases = Releases::getByDate($date);
        
        $leaderboard_names = array();

        if(!empty($releases)) {
            foreach($releases as $release) {
                $release_name = $release['name'];
                
                $leaderboard_name = "{$date->format('j/n/Y')}_PROD";
            
                if($release_name == 'amplified_dlc') {
                    $leaderboard_name = "DLC {$leaderboard_name}";
                }
                
                $leaderboard_names[] = $leaderboard_name;
            }
        }
        
        return $leaderboard_names;
    }
    
    public static function getCsvFilePath(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        
        return "{$installation_path}/assets/files/leaderboard_csv/{$date->format('Y-m-d')}";
    }
    
    public static function getTempCsvFilePath(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        
        return "{$installation_path}/assets/files/leaderboard_csv/temp/{$date->format('Y-m-d')}";
    }
    
    public static function getS3CsvFilePath(DateTime $date) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        
        return "{$installation_path}/assets/files/leaderboard_csv/s3_queue/{$date->format('Y-m-d')}";
    }
    
    public static function getNamesPath(DateTime $date) {
        return static::getCsvFilePath($date) . '/leaderboards.txt';
    }
    
    public static function getTempNamesPath(DateTime $date) {
        return static::getTempCsvFilePath($date) . '/leaderboards.txt';
    }
    
    public static function saveTempNames(DateTime $date, array $names) {
        $temp_csv_path = static::getTempCsvFilePath($date);
        
        if(!is_dir($temp_csv_path)) {
            mkdir($temp_csv_path);
        }
        
        $names_path = "{$temp_csv_path}/leaderboards.txt";
        
        file_put_contents($names_path, implode("\n", $names));
        
        return $names_path;
    }
    
    public static function deleteTempCsv(DateTime $date) {
        $temp_csv_path = static::getTempCsvFilePath($date);
        
        if(is_dir($temp_csv_path)) {
            File::deleteDirectoryRecursive($temp_csv_path);
        }
    }
    
    public static function runClientDownloader($names_path) {
        $module_configuration = Module::getInstance('necrolab')->configuration;
    
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        
        $downloader_path = "{$installation_path}/external/SteamLeaderboardsDownloader3/SteamLB.exe";
        
        $plaintext_password = Encryption::decrypt($module_configuration->steam_client_password);
        
        $save_path = dirname($names_path);
        
        exec("cd {$save_path} && /usr/bin/mono {$downloader_path} {$module_configuration->steam_client_username} {$plaintext_password} {$module_configuration->steam_original_appid} {$names_path}");
        
        unset($plaintext_password);
    }
    
    public static function compressTempToSavedCsv(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
    
        $temp_path = static::getTempCsvFilePath($date);
        $temp_parent_path = dirname($temp_path);
        
        $zip_snapshot_path = static::getCsvFilePath($date) . ".zip";
    
        /* 
            Since this will only run on the backend this would be simplest way to compress an entire folder.
            TODO: Implement a method in the File utility to recursively compress all files in a file using ZipArchive.
        */
        exec("cd {$temp_parent_path} ; zip -r {$date_formatted}.zip {$date_formatted}; rm -rf {$zip_snapshot_path}; mv {$date_formatted}.zip {$zip_snapshot_path}");
    
        return $zip_snapshot_path;
    }
    
    public static function decompressToTempCsv(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
        
        $temp_path = static::getTempCsvFilePath($date);
        $temp_parent_path = dirname($temp_path);
        
        $zip_snapshot_path = static::getCsvFilePath($date) . ".zip";
    
        /* 
            Since this will only run on the backend this would be simplest way to decompress an entire folder.
            TODO: Implement a method in the File utility to recursively decompress all files in a file using ZipArchive.
        */
        exec("unzip {$zip_snapshot_path} -d {$temp_parent_path}");
    }
    
    public static function getTempCsvFiles(DateTime $date) {  
        $snapshot_path = static::getTempCsvFilePath($date);
        
        $csv_files = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, "/^.+\.csv$/i", RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                $file_name = $matched_file[0];
                $file_name_split = explode('/', $matched_file[0]);
                
                $csv_file_name = array_pop($file_name_split);
                $csv_file_name_split = explode('.', $csv_file_name);
                
                $lbid = (int)$csv_file_name_split[0];
                    
                $csv_files[$lbid] = $matched_file[0];
            }
        }
        
        return $csv_files;
    }

    public static function copyZippedCsvToS3(DateTime $date) {
        $date_formatted = $date->format('Y-m-d');
    
        $s3_zip_path = static::getS3CsvFilePath($date) . 'zip';
        
        $zip_snapshot_path = static::getCsvFilePath($date) . ".zip";
    
        /* 
            Since this will only run on the backend this would be simplest way to decompress an entire folder.
            TODO: Implement a method in the File utility to copy this file over.
        */
        exec("cp {$zip_snapshot_path} {$s3_zip_path}");
        
        return $s3_zip_path;
    }
    
    public static function deleteS3ZippedCsv(DateTime $date) {
        $snapshot_path = static::getS3CsvFilePath($date) . 'zip';
    
        if(is_file($snapshot_path)) {
            unlink($snapshot_path);
        }
    }
    
    public static function getCsvUrls() {
        $start_date = new DateTime('2017-01-01');
        $end_date = new DateTime(date('Y-m-d'));
        
        $current_date = clone $start_date;
        
        $csv_urls = array();
        
        while($current_date <= $end_date) {
            $csv_urls[] = "https://necrolab.s3.amazonaws.com/leaderboard_csv/{$current_date->format('Y-m-d')}.zip";
        
            $current_date->add(new DateInterval('P1D'));
        }
        
        return $csv_urls;
    }
    
    public static function getCsvSaveQueueName() {
        return "save_csv";
    }
    
    public static function addToCsvSaveQueue(DateTime $date) {
        static::addDateToQueue(static::getCsvSaveQueueName(), $date);
    }
    
    public static function getCsvUploadQueueName() {
        return "upload_csv";
    }
    
    public static function addToCsvUploadQueue(DateTime $date) {        
        static::addDateToQueue(static::getCsvUploadQueueName(), $date);
    }
    
    public static function getFormattedApiRecord($data_row) {    
        $character = Characters::getById($data_row['character_id']);
    
        $formatted_record = array(
            'lbid' => (int)$data_row['lbid'],
            'name' => $data_row['name'],
            'display_name' => $data_row['display_name'],
            'entries_url' => $data_row['url'],
            'character' => $character['name'],
            'character_number' => $character['sort_order'],
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
        
        if(!empty($data_row['mode_id'])) {
            $mode = Modes::getById($data_row['mode_id']);
        
            $formatted_record['mode'] = Modes::getFormattedApiRecord($mode);
        }
        
        if(!empty($data_row['release_id'])) {
            $release = Releases::getById($data_row['release_id']);
            
            $formatted_record['release'] = Releases::getFormattedApiRecord($release);
        }
        
        return $formatted_record;
    }
}