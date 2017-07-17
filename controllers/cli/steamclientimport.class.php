<?php
namespace Modules\Necrolab\Controllers\Cli;

use \Exception;
use \DateTime;
use \DateInterval;
use \stdClass;
use \Aws\Resource\Aws;
use \Aws\S3\S3Client;
use \Framework\Core\Controllers\Cli;
use \Framework\Utilities\ParallelProcessQueue;
use \Framework\Core\Loader;
use \Framework\Utilities\Encryption;
use \Modules\Necrolab\Models\Leaderboards\Database\Blacklist;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Entries;
use \Modules\Necrolab\Models\Leaderboards\Database\Snapshots;
use \Modules\Necrolab\Models\Leaderboards\Database\Entry;
use \Modules\Necrolab\Models\Leaderboards\Database\Replays;
use \Modules\Necrolab\Models\Leaderboards\Database\Details;
use \Modules\Necrolab\Models\Leaderboards\RecordModels\LeaderboardEntry;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\Leaderboard as DatabaseLeaderboard;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\LeaderboardEntry as DatabaseLeaderboardEntry;
use \Modules\Necrolab\Models\Rankings\Rankings;
use \Modules\Necrolab\Models\Dailies\Rankings\Rankings as DailyRankings;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;
use \Modules\Necrolab\Models\SteamUsers\Database\Pbs as DatabaseSteamUserPbs;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUserPb as DatabaseSteamUserPb;

use \Modules\Necrolab\Models\Characters;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\Releases;

class SteamClientImport
extends Cli {
    protected $as_of_date; 
    
    public function importCsvChunk($chunk_number, $names_path) {
        Leaderboards::runClientDownloader($names_path, $chunk_number);
        
        exit;
    }
    
    protected function importCsv(DateTime $date) {    
        $leaderboard_names = Leaderboards::generateAllNames($date);
        $daily_leaderboard_names = Leaderboards::generateDailyNames($date);
        
        $leaderboard_names = array_merge($leaderboard_names, $daily_leaderboard_names);
        
        $leaderboard_name_chunks = Leaderboards::getNameChunks($leaderboard_names);
        
        Leaderboards::deleteTempCsv($date);

        /*$leaderboard_chunk_paths = array();
        
        foreach($leaderboard_name_chunks as $chunk_number => $leaderboard_name_chunk) {
            $leaderboard_chunk_paths[$chunk_number] = Leaderboards::saveTempNames($date, $leaderboard_names, $chunk_number);
        }*/
        
        $names_path = Leaderboards::saveTempNames($date, $leaderboard_names);
        
        /*if(!empty($leaderboard_chunk_paths)) {
            $csv_import_job_queue = new ParallelProcessQueue();
        
            foreach($leaderboard_chunk_paths as $chunk_number => $leaderboard_chunk_path) {                
                $csv_import_job_queue->setMaxParallelProcesses(2);
    
                $csv_import_job_queue->addProcessToQueue(array($this, 'importCsvChunk'), array(
                    'chunk_number' => $chunk_number,
                    'names_path' => $leaderboard_chunk_path
                ));
            }
            
            $csv_import_job_queue->run();
        }*/
        
        Leaderboards::runClientDownloader($names_path);
        
        //Leaderboards::deleteTempChunks($date);
        
        Leaderboards::compressTempToSavedCsv($date);
        
        Leaderboards::deleteTempCsv($date);
        
        Leaderboards::addToCsvSaveQueue($date);
        Leaderboards::addToCsvUploadQueue($date);
    }
    
    public function actionImportCsv($date = NULL) {
        $this->importCsv(new DateTime($date));
    }
    
    protected function saveCsv(DateTime $date) {
        Leaderboards::deleteTempCsv($date);
        Leaderboards::decompressToTempCsv($date);
        
        $csv_files = Leaderboards::getTempCsvFiles($date, true);

        if(!empty($csv_files)) {
            db()->beginTransaction();
            
            DatabaseSteamUsers::createTemporaryTable();
            Replays::createTemporaryTable();
            DatabaseSteamUserPbs::createTemporaryTable();
            Entries::createTemporaryTable();

            $steam_users_temp_insert_queue = DatabaseSteamUsers::getTempInsertQueue();
            $replays_temp_insert_queue = Replays::getTempInsertQueue();
            $pbs_temp_insert_queue = DatabaseSteamUserPbs::getTempInsertQueue();
            $leaderboard_entries_insert_queue = Entries::getTempInsertQueue();
        
            foreach($csv_files as $lbid => $csv_file) {
                $csv_file_handle = fopen($csv_file, 'r');
                
                $leaderboard_name_row = fgetcsv($csv_file_handle);
                
                $leaderboard_name = $leaderboard_name_row[0];
                
                $leaderboard = new stdClass();
                
                $leaderboard->lbid = $lbid;
                $leaderboard->name = $leaderboard_name;
                $leaderboard->display_name = '';
                $leaderboard->url = '';                
            
                $database_leaderboard = new DatabaseLeaderboard();
                
                $database_leaderboard->setPropertiesFromObject($leaderboard);
                               
                $leaderboard_id = Leaderboards::save($database_leaderboard);
                
                $database_leaderboard->leaderboard_id = $leaderboard_id;
                    
                $leaderboard_snapshot_id = Snapshots::save($database_leaderboard, $date);
                
                Entries::clear($leaderboard_snapshot_id, $date);
                
                $rank = 1;

                while($entry = fgetcsv($csv_file_handle)) {
                    $entry_object = new stdClass();
                    $entry_object->steamid = $entry[0];
                    $entry_object->score = $entry[2];
                    $entry_object->ugcid = $entry[3];
                    $entry_object->zone = $entry[4];
                    $entry_object->level = $entry[5];
                    $entry_object->details = '';
                
                    $steam_user_id = DatabaseSteamUsers::getId($entry_object->steamid);
                    
                    if(empty($steam_user_id)) {                                                                        
                        $steam_user_id = DatabaseSteamUsers::saveToQueue($entry_object->steamid, $steam_users_temp_insert_queue);
                    }
                    
                    $steam_replay_id = Replays::save($entry_object->ugcid, $steam_user_id, $replays_temp_insert_queue);
                    
                    $steam_user_pb_id = DatabaseSteamUserPbs::getId($leaderboard_id, $steam_user_id, $entry_object->score);
                    
                    if(empty($steam_user_pb_id)) {
                        $database_steam_user_pb = new DatabaseSteamUserPb();
                        
                        $database_steam_user_pb->setPropertiesFromSteamObject($entry_object, $database_leaderboard, $rank, $date);
                        
                        if($database_steam_user_pb->isValid($database_leaderboard)) {
                            $database_steam_user_pb->steam_user_id = $steam_user_id;
                            $database_steam_user_pb->leaderboard_id = $leaderboard_id;
                            $database_steam_user_pb->first_leaderboard_snapshot_id = $leaderboard_snapshot_id;
                            $database_steam_user_pb->steam_replay_id = $steam_replay_id;
                        
                            $steam_user_pb_id = DatabaseSteamUserPbs::save($database_steam_user_pb, $pbs_temp_insert_queue);
                        }
                    }
                    
                    if(!empty($steam_user_pb_id)) {             
                        $leaderboard_entries_insert_queue->addRecord(array(
                            'leaderboard_snapshot_id' => $leaderboard_snapshot_id,
                            'steam_user_pb_id' => $steam_user_pb_id,
                            'rank' => $rank
                        ));
                        
                        $rank += 1;
                    }
                }
            }
            
            $steam_users_temp_insert_queue->commit();
            $replays_temp_insert_queue->commit();
            $pbs_temp_insert_queue->commit();
            $leaderboard_entries_insert_queue->commit();
            
            //Save users
            DatabaseSteamUsers::dropTableIndexes();
            
            DatabaseSteamUsers::saveNewTemp();
            
            DatabaseSteamUsers::createTableIndexes();
            
            //Save replays
            Replays::dropTableIndexes();
            
            Replays::saveNewTemp();
            
            Replays::createTableIndexes();
            
            //Save user pbs
            DatabaseSteamUserPbs::dropTableIndexes();
            
            DatabaseSteamUserPbs::saveNewTemp();
            
            DatabaseSteamUserPbs::createTableIndexes();
            
            //Save leaderboard entries
            Entries::dropPartitionTableIndexes($date);
            
            Entries::saveTempEntries($date);
            
            Entries::createPartitionTableIndexes($date);
            
            db()->commit();
            
            Leaderboards::vacuum();
            Snapshots::vacuum();
            DatabaseSteamUserPbs::vacuum();
            Entries::vacuum($date);
            
            Leaderboards::deleteTempCsv($date);
        
            Rankings::addToGenerateQueue($date);
            DailyRankings::addToGenerateQueue($date);
            
            DatabaseSteamUsers::addToCacheQueue();
            DatabaseSteamUserPbs::addToCacheQueue();
            Replays::addToCacheQueue();
            Entries::addToCacheQueue($date);
            Entries::addToDailyCacheQueue($date);
        }
    }
    
    public function actionSaveCsv($date = NULL) { 
        $this->saveCsv(new DateTime($date));
    }
    
    public function actionSaveRangeCsv($start_date, $end_date) {    
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            $this->saveCsv($current_date);
        
            $current_date->add(new DateInterval('P1D'));
        }
    }
    
    public function saveCsvQueueMessageReceived($message) {
        $this->actionSaveCsv($message->body);
    }
    
    public function actionRunCsvSaveQueueListener() {    
        Leaderboards::runQueue(Leaderboards::getCsvSaveQueueName(), array(
            $this,
            'saveCsvQueueMessageReceived'
        ));
    }
    
    protected function uploadCsvToS3(DateTime $date) {
        $this->as_of_date = clone $date;

        Leaderboards::deleteS3ZippedCsv($this->as_of_date);
        
        Loader::load('autoload.php', true, false);
    
        $aws_client = new Aws(array(
            'version' => $this->module->configuration->aws_s3_version,
            'region' => $this->module->configuration->aws_s3_region,
            'credentials' => array(
                'key'    => Encryption::decrypt($this->module->configuration->aws_s3_write_key),
                'secret' => Encryption::decrypt($this->module->configuration->aws_s3_write_secret)
            )
        ));

        $s3_client = $aws_client->s3;
        
        $bucket = $s3_client->bucket('necrolab');
        
        $s3_file_path = Leaderboards::copyZippedCsvToS3($this->as_of_date);
        
        $zipped_file_handle = fopen($s3_file_path, 'r');
        
        $object = $bucket->putObject([
            'Key'  => "leaderboard_csv/{$this->as_of_date->format('Y-m-d')}.zip",
            'Body' => $zipped_file_handle,
        ]);
        
        fclose($zipped_file_handle);

        Leaderboards::deleteS3ZippedCsv($this->as_of_date);
    }
    
    public function actionUploadCsvToS3($date = NULL) {
        $this->uploadCsvToS3(new DateTime($date));
    }
    
    public function actionUploadRangeCsvToS3($start_date, $end_date) {    
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            $this->uploadCsvToS3($current_date);
        
            $current_date->add(new DateInterval('P1D'));
        }
    }
    
    public function uploadCsvQueueMessageReceived($message) {
        $this->actionUploadCsvToS3($message->body);
    }
    
    public function actionRunCsvUploadQueueListener() {    
        Leaderboards::runQueue(Leaderboards::getCsvUploadQueueName(), array(
            $this,
            'uploadCsvQueueMessageReceived'
        ));
    }
}