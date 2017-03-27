<?php
namespace Modules\Necrolab\Controllers\Cli;

use \Exception;
use \DateTime;
use \DateInterval;
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
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;
use \Modules\Necrolab\Models\SteamUsers\Database\Pbs as DatabaseSteamUserPbs;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUserPb as DatabaseSteamUserPb;

class SteamImport
extends Cli {
    protected $as_of_date; 
    
    public function importXmlChildProcess($leaderboard) { 
        $lbid = $leaderboard->lbid;
        $next_page_url = $leaderboard->url;
        
        $page = 1;

        do {
            $retrieval_attempts = 1;
            $retrieval_successful = false;
            $entries_xml = NULL;
            
            while($retrieval_successful == false && $retrieval_attempts <= 5) {
                try {
                    $entries_xml = Leaderboards::getSteamXml($next_page_url);

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
                throw new Exception("Retrieval for lbid {$lbid} has failed.");
            }
            
            if(!empty($entries_xml)) {
                Entries::saveXml($lbid, $this->as_of_date, $page, $entries_xml);
                
                $parsed_xml = Leaderboards::getParsedXml($entries_xml);
            
                if(!empty($parsed_xml->nextRequestURL)) {                    
                    $next_page_url = trim($parsed_xml->nextRequestURL);
                }
                else {
                    $next_page_url = NULL;
                }
            }
            
            $page += 1;
        }
        while(!empty($next_page_url));
    }
    
    public function actionImportXml() {
        $this->as_of_date = new DateTime();
        
        Leaderboards::deleteXml($this->as_of_date);
    
        $leaderboards_xml = Leaderboards::getSteamXml($this->module->configuration->leaderboard_url);
        
        if(!empty($leaderboards_xml)) {
            Leaderboards::saveXml($this->as_of_date, $leaderboards_xml);
            
            $parsed_xml = Leaderboards::getParsedXml($leaderboards_xml);
        
            if(!empty($parsed_xml->leaderboard)) {
                $leaderboard_import_job_queue = new ParallelProcessQueue();
            
                foreach($parsed_xml->leaderboard as $leaderboard) {                
                    $leaderboard_import_job_queue->setMaxParallelProcesses(50);
        
                    if($leaderboard->entries > 0) {
                        $leaderboard_import_job_queue->addProcessToQueue(array($this, 'importXmlChildProcess'), array(
                            'leaderboard' => $leaderboard
                        ));
                    }
                }
                
                $leaderboard_import_job_queue->run();
            }
        }
    }
    
    protected function saveXml(DateTime $date) {
        $this->as_of_date = $date;
        
        Leaderboards::copyXmlToTempFolder($this->as_of_date);
        
        $xml_file_groups = Leaderboards::getXmlFiles($this->as_of_date, true);
        
        $leaderboards_xml = Leaderboards::getXml($xml_file_groups['leaderboards_xml']);
        $parsed_xml = Leaderboards::getParsedXml($leaderboards_xml);

        unset($xml_file_groups['leaderboards_xml']);
        unset($leaderboards_xml);
        
        if(!empty($parsed_xml->leaderboard)) {
            db()->beginTransaction();
        
            foreach($parsed_xml->leaderboard as $leaderboard) {
                $database_leaderboard = new DatabaseLeaderboard();
                
                $database_leaderboard->setPropertiesFromObject($leaderboard);
                
                $lbid = $database_leaderboard->lbid;
                
                if($database_leaderboard->isValid($this->as_of_date)) {                
                    $leaderboard_id = Leaderboards::save($database_leaderboard);
                    
                    $database_leaderboard->leaderboard_id = $leaderboard_id;
                    
                    if(!empty($xml_file_groups[$lbid])) {
                        $xml_file_group = $xml_file_groups[$lbid];
                        
                        $leaderboard_snapshot_id = Snapshots::save($database_leaderboard, $this->as_of_date);
                        
                        Entries::clear($leaderboard_snapshot_id, $this->as_of_date);
                        
                        $rank = 1;
                        
                        foreach($xml_file_group as $page => $xml_file) {
                            $entries_xml = Leaderboards::getXml($xml_file);
                            
                            $parsed_xml = Leaderboards::getParsedXml($entries_xml);
                            
                            unset($entries_xml);
                            
                            if(!empty($parsed_xml->entries->entry)) {
                                $entries = $parsed_xml->entries->entry;
                                
                                if(!is_array($entries)) {
                                    $entries = array($entries);
                                }
                            
                                foreach($entries as $entry) {                                    
                                    $steam_user_id = DatabaseSteamUsers::getId($entry->steamid);
                                    
                                    if(empty($steam_user_id)) {                                    
                                        $database_steam_user = new DatabaseSteamUser();
                                        
                                        $database_steam_user->steamid = $entry->steamid;
                                    
                                        $steam_user_id = DatabaseSteamUsers::save($database_steam_user, 'steam_import');
                                    }
                                    
                                    $score = $entry->score;
                                    
                                    $steam_user_pb_id = DatabaseSteamUserPbs::getId($leaderboard_id, $steam_user_id, $score);
                                    
                                    if(empty($steam_user_pb_id)) {
                                        $database_steam_user_pb = new DatabaseSteamUserPb();
                                        
                                        $database_steam_user_pb->setPropertiesFromSteamObject($entry, $database_leaderboard, $rank, $this->as_of_date);
                                        
                                        if($database_steam_user_pb->isValid($database_leaderboard)) {
                                            $database_steam_user_pb->leaderboard_id = $leaderboard_id;
                                            $database_steam_user_pb->first_leaderboard_snapshot_id = $leaderboard_snapshot_id;
                                        
                                            $steam_user_pb_id = DatabaseSteamUserPbs::save($database_steam_user_pb, 'steam_user_pb_xml_save');
                                        }
                                    }
                                    
                                    if(!empty($steam_user_pb_id)) {                                        
                                        Entry::save($this->as_of_date, array(
                                            'leaderboard_snapshot_id' => $leaderboard_snapshot_id,
                                            'steam_user_pb_id' => $steam_user_pb_id,
                                            'rank' => $rank
                                        ), "save_steam_xml_{$this->as_of_date->format('Y_m')}");
                                        
                                        $rank += 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            db()->commit();
            
            Leaderboards::vacuum();
            Snapshots::vacuum();
            Entries::vacuum($this->as_of_date);
            DatabaseSteamUserPbs::vacuum();
            Details::vacuum();
        }
        
        Leaderboards::deleteTempXml($this->as_of_date);
    }
    
    public function actionSaveXml($date = NULL) { 
        if(empty($date)) {
            $date = date('Y-m-d');
        }
    
        $this->saveXml(new DateTime($date));
    }
    
    public function actionSaveRangeXml($start_date, $end_date) {    
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            $this->saveXml($current_date);
        
            $current_date->add(new DateInterval('P1D'));
        }
    }
    
    protected function uploadXmlToS3(DateTime $date) {
        $this->as_of_date = clone $date;
        
        Leaderboards::deleteS3Xml($this->as_of_date);
    
        $xml_file_groups = Leaderboards::getXmlFiles($this->as_of_date);
        
        if(!empty($xml_file_groups)) {
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
        
            $leaderboards_xml_path = $xml_file_groups['leaderboards_xml'];
        
            $leaderboards_xml = Leaderboards::getXml($leaderboards_xml_path);
            
            Leaderboards::saveS3Xml($this->as_of_date, $leaderboards_xml);
            
            unset($xml_file_groups['leaderboards_xml']);
            unset($leaderboards_xml);
        
            foreach($xml_file_groups as $lbid => $xml_file_group) {
                if(!empty($xml_file_group)) {
                    foreach($xml_file_group as $page => $xml_file) {
                        $entries_xml = Leaderboards::getXml($xml_file);
                        
                        Entries::saveS3Xml($lbid, $this->as_of_date, $page, $entries_xml);
                    }
                }
            }
            
            $s3_file_path = Leaderboards::compressS3Xml($this->as_of_date);
            
            $zipped_file_handle = fopen($s3_file_path, 'r');
            
            $object = $bucket->putObject([
                'Key'  => "leaderboard_xml/{$this->as_of_date->format('Y-m-d')}.zip",
                'Body' => $zipped_file_handle,
            ]);
            
            fclose($zipped_file_handle);
            
            Leaderboards::deleteS3ZippedXml($this->as_of_date);
        }
    }
    
    public function actionUploadXmlToS3($date = NULL) {
        $this->uploadXmlToS3(new DateTime($date));
    }
    
    public function actionUploadRangeXmlToS3($start_date, $end_date) {    
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            $this->uploadXmlToS3($current_date);
        
            $current_date->add(new DateInterval('P1D'));
        }
    }
    
    public function actionImportFromCsv($date, $csv_path) {
        if(!is_readable($csv_path)) {
            throw new Exception("Specified csv path '{$csv_path}' is not valid.");
        }
        
        $date = new DateTime($date);
        
        $csv_file_handle = fopen($csv_path, "r");
        
        $header = fgetcsv($csv_file_handle);
        
        db()->beginTransaction();
        
        while($csv_row = fgetcsv($csv_file_handle)) {
            $data_row = array_combine($header, $csv_row);

            $steam_replay_id = Replays::save($data_row['ugcid'], $data_row['steam_user_id']);
            $leaderboard_entry_details_id = Details::save($data_row['details']);
        
            $database_entry = new DatabaseLeaderboardEntry();
            
            $database_entry->setPropertiesFromArray($data_row);
            
            $database_entry->steam_replay_id = $steam_replay_id;
            $database_entry->leaderboard_entry_details_id = $leaderboard_entry_details_id;
            
            Entry::save($date, $database_entry);  
        }
        
        db()->commit();
        
        Leaderboards::vacuum();
        Snapshots::vacuum();
        Entries::vacuum($date);
        DatabaseSteamUserPbs::vacuum();
        Details::vacuum();
    }
    
    protected function recompressXml(DateTime $date) {
        $xml_file_groups = Leaderboards::getXmlFiles($date);
        
        if(!empty($xml_file_groups)) {
            $leaderboards_xml = Leaderboards::getOldXml($xml_file_groups['leaderboards_xml']);
            unset($xml_file_groups['leaderboards_xml']);
            
            Leaderboards::saveXml($date, $leaderboards_xml);
            
            foreach($xml_file_groups as $lbid => $xml_file_group) {
                foreach($xml_file_group as $page => $xml_file) {
                    $entries_xml = Leaderboards::getOldXml($xml_file);
                    
                    Entries::saveXml($lbid, $date, $page, $entries_xml);
                }
            }
        }
    }
    
    public function actionRecompressXml($date = NULL) {
        $date = new DateTime($date);
        
        $this->recompressXml($date);
    }
    
    public function actionRecompressRangeXml($start_date, $end_date) {    
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            $this->recompressXml($current_date);
        
            $current_date->add(new DateInterval('P1D'));
        }
    }
    
    public function actionCreateEntriesParition($date = NULL) {
        $date = new DateTime($date);
    
        Entries::createPartitionTable($date);
    }
    
    public function actionCreateNextMonthEntriesPartition($date = NULL) {
        $date = new DateTime($date);
        
        $date->add(new DateInterval('P1M'));
        
        Entries::createPartitionTable($date);
    }
    
    public function actionCreateEntriesParitions($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
    
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            Entries::createPartitionTable($current_date);
        
            $current_date->add(new DateInterval('P1M'));
        }
    }
}