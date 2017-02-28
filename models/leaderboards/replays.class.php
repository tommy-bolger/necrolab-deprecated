<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Exception;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \RegexIterator;
use \RecursiveRegexIterator;
use \Framework\Modules\Module;
use \Framework\Utilities\File;
use \Modules\Necrolab\Models\Necrolab;

class Replays
extends Necrolab {
    protected static $replays = array();
    
    public static function loadAll() {}
    
    public static function get($ugcid) {
        static::loadAll();
        
        $steam_replay_id = NULL;
        
        if(isset(static::$replays[$ugcid])) {
            $steam_replay_id = static::$replays[$ugcid];
        }
        
        return $steam_replay_id;
    }
    
    /*
        This functionality was borrowed from: https://braceyourselfgames.com/forums/viewtopic.php?f=5&t=3240
        
        All credit goes to blueblimp. Thank you!
    */
    public static function getSeedFromZ1Seed($zone_1_seed) {
        $base = 6;
        $mul = 23987;
        $invmul = 492935547;
        $period = 2 ** 32;
        $overflow = 2 ** 32;
        
        if((($mul * $invmul) % $period) != 1) {
            throw new Exception("((mul * invmul) % period) does not equal one.");
        }
        
        $zone_1_seed = (integer)$zone_1_seed;
        
        $zone_1_seed -= $base;
        
        while($zone_1_seed < 0) {
            $zone_1_seed += $period;
        }
        
        $seed = ($zone_1_seed * $invmul) % $period;
        
        if($seed >= $overflow) {
            $seed -= $period;
        }
        
        return $seed;
    }
    
    /*
        This functionality was borrowed from: https://github.com/necrommunity/replay-parser/blob/master/js/main.js#L5
        
        All credit goes to AlexisYJ and Grimy. Thank you!
    */
    public static function getDLCSeedFromZ1Seed($zone_1_seed) {
        $zone_1_seed = intval($zone_1_seed);
        
        $added_seed = $zone_1_seed + 1073765959;
        
        $multiplied_seed = $added_seed * 225371434;
        
        $modulus_seed = $multiplied_seed % 2147483647;
        
        $seed = $modulus_seed % 1899818559;
        
        return $seed;
    }
    
    public static function getFileData($url) {    
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        
        $replay_data = curl_exec($request);
        
        $http_response_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
        
        $request_error = curl_error($request);
        
        if(!empty($request_error)) {
            $error_code = curl_errno($request);
        
            if(!empty($error_code)) {
                $error_message = curl_strerror($error_code);
                
                throw new Exception("HTTP request to Steam replay remote storage encountered an error with number '{$error_code}' and message '{$error_message}'.");
            }
            else {
                throw new Exception('Response from Steam replay remote storage returned with an unknown error.');
            }
        }
        
        curl_close($request);
        
        return $replay_data;
    }
    
    public static function getHttpFilePath($ugcid) {
        $http_file_path = Module::getInstance('necrolab')->getFilesHttpPath();
        return "{$http_file_path}/steam_replays/original/{$ugcid}.gz";
    }
    
    public static function getFilePath($ugcid) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        return "{$installation_path}/assets/files/steam_replays/original/{$ugcid}.gz";
    }
    
    public static function getFile($file_path) {    
        return gzdecode(file_get_contents($file_path));
    }
    
    public static function saveTempFile($ugcid, $file_data) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/assets/files/steam_replays/original/temp";
        
        if(!is_writable($snapshot_path)) {
            throw new Exception("Directory '{$snapshot_path}' does not exist or is not writable.");
        }
    
        file_put_contents("{$snapshot_path}/{$ugcid}.gz", gzencode($file_data, 9));
    }
    
    public static function getTempFiles() {  
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/assets/files/steam_replays/original/temp";
        
        $temp_files = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, '/^.+\.gz$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                $ugcid = str_replace(array(
                    "{$snapshot_path}/",
                    '.gz'
                ), '', $matched_file);
            
                $temp_files[$ugcid[0]] = array(
                    'file_path' => $matched_file[0],
                    'destination_file_path' => str_replace('temp/', '', $matched_file[0])
                );
            }
        }
        
        return $temp_files;
    }
    
    public static function saveInvalidFile($steam_replay_id) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/assets/files/steam_replays/original/invalid";
        
        if(!is_writable($snapshot_path)) {
            throw new Exception("Directory '{$snapshot_path}' does not exist or is not writable.");
        }
    
        file_put_contents("{$snapshot_path}/{$steam_replay_id}.invalid", '.');
    }
    
    public static function getInvalidFiles() {  
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/assets/files/steam_replays/original/invalid";
        
        $invalid_files = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, '/^.+\.invalid$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                $steam_replay_id = str_replace(array(
                    "{$snapshot_path}/",
                    '.invalid'
                ), '', $matched_file);
            
                $invalid_files[$steam_replay_id[0]] = $matched_file[0];
            }
        }
        
        return $invalid_files;
    }
    
    public static function getS3QueueFilePath($ugcid) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        return "{$installation_path}/assets/files/steam_replays/original/s3_queue/{$ugcid}";
    }
    
    public static function compressS3QueueFile($ugcid) {
        return File::zipFile(static::getS3QueueFilePath($ugcid));
    }
    
    public static function deleteS3QueueFile($ugcid) {
        unlink(static::getS3QueueFilePath($ugcid));
    }
    
    public static function getS3QueueZippedFilePath($ugcid) {
        return static::getS3QueueFilePath($ugcid) . '.zip';
    }
    
    public static function deleteS3ZippedQueueFile($ugcid) {
        unlink(static::getS3QueueZippedFilePath($ugcid));
    }
    
    public static function addFileToS3Queue($ugcid) {
        $replay_file_data = static::getFile(static::getFilePath($ugcid));
        
        file_put_contents(static::getS3QueueFilePath($ugcid), $replay_file_data);
        
        static::compressS3QueueFile($ugcid);
        
        static::deleteS3QueueFile($ugcid);
        
        return static::getS3QueueZippedFilePath($ugcid);
    }
}