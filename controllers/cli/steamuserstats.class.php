<?php
namespace Modules\Necrolab\Controllers\Cli;

use \Exception;
use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Framework\Api\Steam\ISteamUserStats;
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
    
    public function processUserAchievementsChunk($achievements_json) {
        if(!empty($achievements_json)) {        
            foreach($achievements_json as $achievement_json) {
                $player_achievements = json_decode($achievement_json, true);

                if(
                    !empty($player_achievements) && 
                    !empty($player_achievements['playerstats']) && 
                    !empty($player_achievements['playerstats']['steamID'])
                ) {                
                    $steamid = $player_achievements['playerstats']['steamID'];
                    
                    $steam_user_id = $this->steam_user_ids[$steamid];
                    
                    DatabaseSteamUserAchievements::saveJson($steam_user_id, $achievement_json);
                }
            }
        }
    }
    
    public function actionImportUserAchievements() {    
        $this->steam_user_ids = DatabaseSteamUsers::getAllIds();

        if(!empty($this->steam_user_ids)) {
            $steam_api = $this->getSteamUserStatsApi();
            
            $request_queue = new AsyncMultiRestQueue();
            
            $request_queue->setCommitCallback(array(
                $this,
                'processUserAchievementsChunk'
            ));
        
            foreach($this->steam_user_ids as $steamid => $steam_user_id) {
                $steam_api->getPlayerAchievements($steamid, $this->app_id);

                $request_queue->addRequest($steam_api->getRequest());
            }
            
            $request_queue->commit();
        }
    }
    
    public function actionSaveUserAchievements() {        
        $json_files = DatabaseSteamUserAchievements::getJsonFiles();
        
        if(!empty($json_files)) {
            $achievement_ids_by_name = AllAchievements::getAllIdsByName();
        
            db()->beginTransaction();
            
            DatabaseSteamUserAchievements::createTemporaryTable();
            
            $temp_insert_queue = DatabaseSteamUserAchievements::getTempInsertQueue();
        
            foreach($json_files as $steam_user_id => $json_file) {
                $unparsed_json = DatabaseSteamUserAchievements::getJson($json_file);
            
                $parsed_json = json_decode($unparsed_json, true);
                    
                unset($unparsed_json);
            
                if(!empty($parsed_json['playerstats']) && !empty($parsed_json['playerstats']['achievements'])) {
                    $player_achievements = $parsed_json['playerstats']['achievements'];
                    
                    foreach($player_achievements as $player_achievement) {
                        if(!empty($player_achievement['unlocktime'])) {
                            $achievement_name = strtoupper($player_achievement['apiname']); 
                            
                            if(!empty($achievement_ids_by_name[$achievement_name])) {
                                $achievement_id = $achievement_ids_by_name[$achievement_name];
                            
                                $achieved = new DateTime();
                                    
                                $achieved->setTimestamp($player_achievement['unlocktime']);

                                $temp_insert_queue->addRecord(array(
                                    'steam_user_id' => $steam_user_id,
                                    'achievement_id' => $achievement_id,
                                    'achieved' => $achieved->format('Y-m-d H:i:s')
                                ));
                            }
                        }
                    }
                }
                
                DatabaseSteamUserAchievements::deleteJson($json_file);
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