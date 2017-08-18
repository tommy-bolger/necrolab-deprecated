<?php
namespace Modules\Necrolab\Controllers\Cli;

use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Framework\Api\Steam\ISteamUser;
use \Framework\Utilities\Encryption;
use \Framework\Utilities\RecordQueue;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;

class SteamUsers
extends Cli {     
    protected $date;
    
    protected $group_number;
    
    protected $steam_api;
    
     public function importJsonChildProcess(array $request_steam_ids) {
        if($request_steam_ids) {
            foreach($request_steam_ids as $request_steam_id_group) {
                $this->steam_api->getPlayerSummaries($request_steam_id_group);
                
                $this->steam_api->submitRequest();
                
                DatabaseSteamUsers::saveJson($this->date, $this->group_number, $this->steam_api->getResponse());
                
                $this->group_number += 1;
            }
        }
    }
    
    public function actionImportJson() {
        $this->date = new DateTime();
    
        $users_to_update = DatabaseSteamUsers::getOutdatedIds();
        
        if(!empty($users_to_update)) {
            $this->group_number = 1;
        
            $this->steam_api = new ISteamUser(); 
            $this->steam_api->setApiKey(Encryption::decrypt($this->module->configuration->steam_api_key), 'key');
            
            DatabaseSteamUsers::deleteJson($this->date);
            
            $record_queue = new RecordQueue();
            
            $record_queue->setCommitCount(1);
            
            $record_queue->setCommitCallback(array(
                $this,
                'importJsonChildProcess'
            ));
            
            $record_queue->setCommitCallbackReattempts(5);
            
            $record_queue->setCommitCallbackReattemptInterval(1);
            
            $steamid_groups = array_chunk($users_to_update, 100);
        
            foreach($steamid_groups as $steamids_group) {
                $record_queue->addRecord($steamids_group);
            }
            
            $record_queue->commit();
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