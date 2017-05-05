<?php
namespace Modules\Necrolab\Controllers\Cli;

use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Framework\Api\Steam\ISteamUserStats;
use \Framework\Utilities\Encryption;
use \Modules\Necrolab\Models\Achievements\Database\Achievements as DatabaseAchievements;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\Achievements as DatabaseSteamUserAchievements;
use \Modules\Necrolab\Models\Achievements\Database\RecordModels\Achievement as DatabaseAchievement;

class SteamUserStats
extends Cli { 
    protected $steam_api;
    
    protected $app_id;
    
    protected $date;
    
    public function init() {
        $this->steam_api = new ISteamUserStats(); 
        $this->steam_api->setApiKey(Encryption::decrypt($this->module->configuration->steam_api_key), 'key');
        
        $this->app_id = $this->module->configuration->steam_original_appid;
    }
    
    public function actionImportAchievements() {
        $game_stats = $this->steam_api->getSchemaForGame($this->app_id);
        
        if(!empty($game_stats->game) && !empty($game_stats->game->availableGameStats) && !empty($game_stats->game->availableGameStats->achievements)) {
            $achievements = $game_stats->game->availableGameStats->achievements;
        
            foreach($achievements as $achievement) {
                $database_achievement = DatabaseAchievements::getByName($achievement->name);

                if(empty($database_achievement)) {
                    $achievement_record = new DatabaseAchievement();
                    
                    $achievement_record->setPropertiesFromSteamObject($achievement);
                    
                    DatabaseAchievements::save($achievement_record, 'achievement_import');
                }
            }
        }
    }
    
    public function actionImportUserAchievements() {    
        $steam_users = DatabaseSteamUsers::getAllIds();
        
        if(!empty($steam_users)) {
            foreach($steam_users as $steamid => $steam_user_id) {
                $steam_user_achievements_xml = NULL;
            
                try {
                    $steam_user_achievements_xml = DatabaseSteamUserAchievements::getSteamXml($steamid, $this->app_id);
                }
                catch(Exception $exception) {}
                
                if(!empty($steam_user_achievements_xml)) {                
                    DatabaseSteamUserAchievements::saveXml($steam_user_id, $steam_user_achievements_xml);
                }
            }
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
                            
                                $achievement_id = DatabaseAchievements::getIdByName($achievement_name);
                                
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
            DatabaseSteamUserAchievements::dropTableConstraints();
            
            DatabaseSteamUserAchievements::saveTemp();
            
            DatabaseSteamUserAchievements::createTableConstraints();
            DatabaseSteamUserAchievements::createTableIndexes();
            
            db()->commit();
            
            DatabaseSteamUserAchievements::vacuum();
        }
    }
}