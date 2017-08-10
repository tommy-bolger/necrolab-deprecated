<?php
namespace Modules\Necrolab\Controllers\Cli;

use \Exception;
use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Framework\Api\Steam\ISteamUserStats;
use \Framework\Api\Steam\UserAchievements;
use \Framework\Api\AsyncMultiRestQueue;
use \Framework\Utilities\Encryption;
use \Modules\Necrolab\Models\Achievements\Achievements as AllAchievements;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\Achievements as DatabaseSteamUserAchievements;
use \Modules\Necrolab\Models\Achievements\Achievement as AchievementRecord;

class SteamUserStats
extends Cli {     
    protected $app_id;
    
    protected $steam_user_ids = array();
    
    public function init() {
        $this->app_id = $this->module->configuration->steam_original_appid;
    }
    
    protected function getSteamUserStatsApi() {
        $steam_api = new ISteamUserStats(); 
        $steam_api->setApiKey(Encryption::decrypt($this->module->configuration->steam_api_key), 'key');
        
        return $steam_api;
    }
    
    public function actionImportAchievements() {
        $steam_api = $this->getSteamUserStatsApi();
    
        $steam_api->getSchemaForGame($this->app_id);
        
        $steam_api->submitRequest();
        
        $game_stats = $steam_api->getParsedResponse();
        
        $steam_api->closeRequest();

        if(!empty($game_stats->game) && !empty($game_stats->game->availableGameStats) && !empty($game_stats->game->availableGameStats->achievements)) {
            $achievements = $game_stats->game->availableGameStats->achievements;
        
            foreach($achievements as $achievement) {
                $database_achievement = AllAchievements::getByName($achievement->name);

                if(empty($database_achievement)) {
                    $achievement_record = new AchievementRecord();
                    
                    $achievement_record->setPropertiesFromSteamObject($achievement);
                    
                    AllAchievements::save($achievement_record, 'achievement_import');
                }
            }
        }
    }
    
    public function processUserAchievementsChunk($achievements_xml) {
        if(!empty($achievements_xml)) {        
            foreach($achievements_xml as $achievement_xml) {
                if(UserAchievements::responseIsValid($achievement_xml)) {
                    $steamid_start_position = strpos($achievement_xml, '<steamID64>') + strlen('<steamID64>');
                    $steamid_end_position = strpos($achievement_xml, '</steamID64>');
                
                    $steamid = substr($achievement_xml, $steamid_start_position, ($steamid_end_position - $steamid_start_position));
                    
                    $steam_user_id = $this->steam_user_ids[$steamid];
                    
                    DatabaseSteamUserAchievements::saveXml($steam_user_id, $achievement_xml);
                }
            }
        }
    }
    
    public function actionImportUserAchievements() {    
        $this->steam_user_ids = DatabaseSteamUsers::getAllIds();
        
        if(!empty($this->steam_user_ids)) {
            $steam_api = new UserAchievements(); 
            $steam_api->setApiKey(Encryption::decrypt($this->module->configuration->steam_api_key), 'key');
            
            $request_queue = new AsyncMultiRestQueue();
            
            $request_queue->setCommitCallback(array(
                $this,
                'processUserAchievementsChunk'
            ));
        
            foreach($this->steam_user_ids as $steamid => $steam_user_id) {
                $steam_api->getUserAchievements($steamid, $this->app_id);

                $request_queue->addRequest($steam_api->getRequest());
            }
            
            $request_queue->commit();
        }
    }
    
    public function actionSaveUserAchievements() {        
        $xml_files = DatabaseSteamUserAchievements::getXmlFiles();
        
        if(!empty($xml_files)) {
            db()->beginTransaction();
            
            DatabaseSteamUserAchievements::createTemporaryTable();
            
            $temp_insert_queue = DatabaseSteamUserAchievements::getTempInsertQueue();
        
            foreach($xml_files as $steam_user_id => $xml_file) {
                $unparsed_xml = DatabaseSteamUserAchievements::getXml($xml_file);
            
                $parsed_xml = DatabaseSteamUserAchievements::getParsedXml($unparsed_xml);
                    
                unset($unparsed_xml);
            
                if(isset($parsed_xml->achievements)) {
                    if(isset($parsed_xml->achievements->achievement)) {
                        $steam_achievements = $parsed_xml->achievements->achievement;
                    
                        foreach($steam_achievements as $steam_achievement) {                           
                            if(!empty($steam_achievement->unlockTimestamp)) {
                                $achievement_name = strtoupper($steam_achievement->apiname); 
                            
                                $achievement_id = AllAchievements::getIdByName($achievement_name);
                                
                                if(!empty($achievement_id)) {
                                    if(!DatabaseSteamUserAchievements::recordExists($steam_user_id, $achievement_id)) {
                                        $achieved = new DateTime();
                                        
                                        $achieved->setTimestamp($steam_achievement->unlockTimestamp);
                                        
                                        $temp_insert_queue->addRecord(array(
                                            'steam_user_id' => $steam_user_id,
                                            'achievement_id' => $achievement_id,
                                            'achieved' => $achieved->format('Y-m-d H:i:s')
                                        ));
                                    }
                                }
                            }
                        }
                    }
                }
                
                DatabaseSteamUserAchievements::deleteXml($xml_file);
            }
            
            $temp_insert_queue->commit();
            
            DatabaseSteamUserAchievements::dropTableIndexes();
            
            DatabaseSteamUserAchievements::saveTemp();
            
            DatabaseSteamUserAchievements::createTableIndexes();
            
            db()->commit();
            
            DatabaseSteamUserAchievements::vacuum();
        }
    }
    
    public function actionImportUserStats() {        
        $this->steam_user_ids = DatabaseSteamUsers::getAllIds();
        
        if(!empty($steam_users)) {
            $steam_api = $this->getSteamUserStatsApi();
        
            foreach($steam_users as $steamid => $steam_user_id) {
                $steam_user_achievements = NULL;
            
                try {
                    $steam_user_achievements = $steam_api->getUserStatsForGame($steamid, $this->app_id);
                }
                catch(Exception $exception) {
                    $steam_user_achievements = NULL;
                }
                
                if(!empty($steam_user_achievements)) { 
                    file_put_contents(__DIR__ . '/stats.txt', var_export($steam_user_achievements, true));
                    //DatabaseSteamUserAchievements::saveXml($steam_user_id, $steam_user_achievements_xml);
                }
            }
        }
    }
}