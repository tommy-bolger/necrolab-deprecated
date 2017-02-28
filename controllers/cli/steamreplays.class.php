<?php
namespace Modules\Necrolab\Controllers\Cli;

use \Exception;
use \DateTime;
use Aws\Resource\Aws;
use Aws\S3\S3Client;
use \Framework\Core\Controllers\Cli;
use \Framework\Utilities\ParallelProcessQueue;
use \Framework\Api\Steam\ISteamRemoteStorage;
use \Framework\Core\Loader;
use \Framework\Utilities\Encryption;
use \Modules\Necrolab\Models\Leaderboards\Database\Replays as DatabaseReplays;
use \Modules\Necrolab\Models\Leaderboards\Database\RunResults as DatabaseRunResults;
use \Modules\Necrolab\Models\Leaderboards\Database\ReplayVersions as DatabaseReplayVersions;
use \Modules\Necrolab\Models\Leaderboards\RecordModels\SteamReplay;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\RunResult as DatabaseRunResult;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\ReplayVersion as DatabaseReplayVersion;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\SteamReplay as DatabaseSteamReplay;

class SteamReplays
extends Cli {
    protected $version;

    protected $steam_api;
    
    protected $appid;
    
    protected $date;
    
    public function init() {
        $this->appid = $this->module->configuration->steam_original_appid;
    }

    public function downloadChildProcess(array $replay_record) {
        $ugcid = $replay_record['ugcid'];
    
        $retrieval_attempts = 1;
        $retrieval_successful = false;
        $replay_meta_data = NULL;
        
        while($retrieval_successful == false && $retrieval_attempts <= 5) {
            try {
                $replay_meta_data = $this->steam_api->getUGCFileDetails($this->appid, $ugcid);

                $retrieval_successful = true;
            }
            catch(Exception $exception) {
                $retrieval_attempts += 1;
                $retrieval_successful = false;

                $this->framework->coutLine("Failed retrieval, making attempt {$retrieval_attempts}.");
                
                sleep(1);
            }
        }
        
        if($retrieval_successful && !empty($replay_meta_data->data->url)) {
            $replay_file_data = DatabaseReplays::getFileData($replay_meta_data->data->url);

            DatabaseReplays::saveTempFile($ugcid, $replay_file_data);
        }
        else {
            DatabaseReplays::saveInvalidFile($replay_record['steam_replay_id']);
        }
    }
    
    public function actionDownload($limit = 10000) {
        $this->date = new DateTime();
    
        $replays_to_update_resultset = DatabaseReplays::getUnsavedReplaysResultset();
        
        $replays_to_update_resultset->setRowsPerPage($limit);
        
        $replays_to_update = $replays_to_update_resultset->prepareExecuteQuery();
        
        $database = db();
        
        $this->steam_api = new ISteamRemoteStorage(); 
        $this->steam_api->setApiKey(Encryption::decrypt($this->module->configuration->steam_api_key), 'key');
        
        while($replay_to_update = $database->getStatementRow($replays_to_update)) {        
            if($replay_to_update['ugcid'] != -1) {
                $this->downloadChildProcess($replay_to_update);
            }
        }
        
        /*
            This code is commented out in favor of the above since running parallel requests heavily slows down this process. 
            It's much faster to request one replay file at a time due to throttling on Steam's side.
        */
        /*$replay_update_job_queue = new ParallelProcessQueue();
        
        $replay_update_job_queue->setMaxParallelProcesses(25);
            
        while($replay_to_update = $database->getStatementRow($replays_to_update)) {        
            if($replay_to_update['ugcid'] != -1) {                
                $replay_update_job_queue->addProcessToQueue(array($this, 'downloadChildProcess'), array(
                    'replay_record' => $replay_to_update
                ));
            }
        }
        
        $replay_update_job_queue->run();*/
    }
    
    public function actionSaveFiles() {
        $temp_replay_files = DatabaseReplays::getTempFiles();
        
        if(!empty($temp_replay_files)) {
            db()->beginTransaction();
        
            foreach($temp_replay_files as $ugcid => $temp_replay_file) {    
                $steam_replay = new SteamReplay();
                $steam_replay->setPropertiesFromReplayFile($temp_replay_file['file_path']);
            
                $successful = rename($temp_replay_file['file_path'], $temp_replay_file['destination_file_path']);
                
                if($successful) {
                    //Run result
                    $run_result_id = NULL;
                    
                    if(!empty($steam_replay->run_result)) {
                        $run_result = new DatabaseRunResult();
                        
                        $run_result->name = $steam_replay->run_result;
                        $run_result->is_win = $steam_replay->is_win;
                        
                        $run_result_id = DatabaseRunResults::save($run_result);
                    }
                    
                    //Steam replay version
                    $replay_version_id = NULL;
                    
                    if(!empty($steam_replay->replay_version)) {
                        $replay_version = new DatabaseReplayVersion();
                        
                        $replay_version->name = $steam_replay->replay_version;
                        
                        $replay_version_id = DatabaseReplayVersions::save($replay_version);
                    }
                    
                    //Steam replay save
                    $steam_replay_record = new DatabaseSteamReplay();
                    
                    $steam_replay_record->seed = $steam_replay->seed;
                    $steam_replay_record->run_result_id = $run_result_id;
                    $steam_replay_record->steam_replay_version_id = $replay_version_id;
                    $steam_replay_record->downloaded = 1;
                    $steam_replay_record->invalid = 0;
                    $steam_replay_record->uploaded_to_s3 = 0;
                    
                    DatabaseReplays::updateBatch(DatabaseReplays::get($ugcid), $steam_replay_record);
                }
            }
            
            db()->commit();
        }
        
        $invalid_replay_files = DatabaseReplays::getInvalidFiles();
        
        if(!empty($invalid_replay_files)) {
            db()->beginTransaction();
        
            foreach($invalid_replay_files as $steam_user_id => $invalid_replay_file) {            
                $successful = unlink($invalid_replay_file);
                
                if($successful) {
                    $steam_replay_record = new DatabaseSteamReplay();
                    
                    $steam_replay_record->downloaded = 0;
                    $steam_replay_record->invalid = 1;
                    $steam_replay_record->uploaded_to_s3 = 0;
                    
                    DatabaseReplays::updateBatch($steam_user_id, $steam_replay_record);
                }
            }
            
            db()->commit();
        }
    }
    
    public function actionUpdateFromFiles() {
        $database = db();
    
        $database->beginTransaction();
    
        $replays_to_update_resultset = DatabaseReplays::getSavedReplaysResultset();
        
        $replays_to_update = $replays_to_update_resultset->prepareExecuteQuery();
        
        while($replay_to_update = $database->getStatementRow($replays_to_update)) { 
            $ugcid = $replay_to_update['ugcid'];

            if($ugcid != -1) {
                $replay_file_path = DatabaseReplays::getFilePath($ugcid);
                
                $steam_replay = new SteamReplay();
                $steam_replay->setPropertiesFromReplayFile($replay_file_path);
                
                //Run result
                $run_result_id = NULL;
                
                if(!empty($steam_replay->run_result)) {
                    $run_result = new DatabaseRunResult();
                    
                    $run_result->name = $steam_replay->run_result;
                    $run_result->is_win = $steam_replay->is_win;
                    
                    $run_result_id = DatabaseRunResults::save($run_result);
                }
                
                //Steam replay version
                $replay_version_id = NULL;
                
                if(!empty($steam_replay->replay_version)) {
                    $replay_version = new DatabaseReplayVersion();
                    
                    $replay_version->name = $steam_replay->replay_version;
                    
                    $replay_version_id = DatabaseReplayVersions::save($replay_version);
                }
                
                //Steam replay save
                $steam_replay_record = new DatabaseSteamReplay();
                
                $seed = $steam_replay->seed;
                
                if(!(empty($seed) && empty($run_result_id) && empty($replay_version_id))) {
                    $steam_replay_record->seed = $seed;
                    $steam_replay_record->run_result_id = $run_result_id;
                    $steam_replay_record->steam_replay_version_id = $replay_version_id;
                    
                    
                    DatabaseReplays::update($replay_to_update['steam_replay_id'], $steam_replay_record);
                }
            }
        }
        
        $database->commit();
    }
    
    public function actionUploadFilesToS3() {        
        $replays_to_upload_resultset = DatabaseReplays::getUnuploadedReplaysResultset();
        
        $replays_to_upload = $replays_to_upload_resultset->prepareExecuteQuery();
        
        $database = db();
        
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
        
        while($replay_to_upload = $database->getStatementRow($replays_to_upload)) { 
            $ugcid = $replay_to_upload['ugcid'];
            
            if($ugcid != -1) {
                $s3_file_path = DatabaseReplays::addFileToS3Queue($ugcid);
                
                $zipped_file_handle = fopen($s3_file_path, 'r');
            
                $object = $bucket->putObject([
                    'Key'  => "replays/{$ugcid}.zip",
                    'Body' => $zipped_file_handle,
                ]);
                
                fclose($zipped_file_handle);
                
                DatabaseReplays::deleteS3ZippedQueueFile($ugcid);
                
                //Steam replay save
                $steam_replay_record = new DatabaseSteamReplay();

                $steam_replay_record->uploaded_to_s3 = 1;
                
                DatabaseReplays::update($replay_to_upload['steam_replay_id'], $steam_replay_record);
            }
        }
    }
}