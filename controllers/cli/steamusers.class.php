<?php
namespace Modules\Necrolab\Controllers\Cli;

use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Framework\Api\Steam\ISteamUser;
use \Framework\Utilities\Encryption;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;

class SteamUsers
extends Cli { 
    protected $steam_api;
    
    protected $date;

    protected function importJsonChildProcess(array $request_steam_ids, $group_number) {        
        $retrieval_attempts = 1;
        $retrieval_successful = false;
        $steam_users_data = NULL;
        
        while($retrieval_successful == false && $retrieval_attempts <= 5) {
            try {
                $steam_users_data = $this->steam_api->getPlayerSummaries($request_steam_ids);

                $retrieval_successful = true;
            }
            catch(Exception $exception) {
                $retrieval_attempts += 1;
                $retrieval_successful = false;
                
                $this->framework->coutLine("Failed retrieval, making attempt {$retrieval_attempts}.");
                
                sleep(1);
            }
        }
        
        if($retrieval_successful == false) {
            throw new Exception("Retrieval for steam user group {$group_number} has failed.");
        }
        
        DatabaseSteamUsers::saveJson($this->date, $group_number, $steam_users_data);
    }
    
    public function actionImportJson() {
        $this->date = new DateTime();
    
        $users_to_update = DatabaseSteamUsers::getOutdatedIds();
        
        if(!empty($users_to_update)) {            
            $this->steam_api = new ISteamUser(); 
            $this->steam_api->setApiKey(Encryption::decrypt($this->module->configuration->steam_api_key), 'key');
            
            DatabaseSteamUsers::deleteJson($this->date);
        
            $steamids_group = array();
            $group_counter = 1;
            $group_number = 1;
        
            foreach($users_to_update as $steam_user_id => $steamid) {
                if($group_counter == 101) {
                    $this->importJsonChildProcess($steamids_group, $group_number);
                    
                    $group_counter = 1;
                    $steamids_group = array();
                    
                    $group_number += 1;
                }
                
                $steamids_group[] = $steamid;
            
                $group_counter += 1;
            }
            
            if(!empty($steamids_group)) {
                $this->importJsonChildProcess($steamids_group, $group_number);
            }
        }
    }
    
    public function actionSaveJson() {
        $this->date = new DateTime();
    
        $steam_user_files = DatabaseSteamUsers::getJsonFiles($this->date);
            
        if(!empty($steam_user_files)) {            
            db()->beginTransaction();
            
            DatabaseSteamUsers::createTemporaryTable();
            
            $temp_insert_queue = DatabaseSteamUsers::getTempInsertQueue();
            
            foreach($steam_user_files as $steam_user_file) {
                $steam_users = DatabaseSteamUsers::getJson($steam_user_file);

                if(!empty($steam_users->response->players)) {
                    foreach($steam_users->response->players as $steam_user) {
                        $steam_user_record = new DatabaseSteamUser();
                        
                        $steam_user_record->setPropertiesFromObject($steam_user);
                        
                        $steam_user_record->updated = date('Y-m-d H:i:s');
                        
                        $temp_record = $steam_user_record->getTempRecordArray();
                        
                        $temp_record['steam_user_id'] = DatabaseSteamUsers::getId($steam_user->steamid);
                        
                        $temp_insert_queue->addRecord($temp_record);
                    }
                }
            }
            
            $temp_insert_queue->commit();
            
            DatabaseSteamUsers::saveTemp();
            
            db()->commit();
            
            DatabaseSteamUsers::vacuum();
            
            DatabaseSteamUsers::deleteJson($this->date);
        }
    }
    
    public function actionLoadIntoCache() {
        DatabaseSteamUsers::loadIntoCache();
    }
    
    public function cacheQueueMessageReceived($message) {
        $this->actionLoadIntoCache($message->body);
    }
    
    public function actionRunCacheQueueListener() {    
        DatabaseSteamUsers::runQueue(DatabaseSteamUsers::getCacheQueueName(), array(
            $this,
            'cacheQueueMessageReceived'
        ));
    }  
}