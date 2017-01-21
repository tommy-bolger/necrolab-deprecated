<?php
namespace Modules\Necrolab\Controllers\Cli;

use \Exception;
use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Framework\Utilities\ParallelProcessQueue;
use \Modules\Necrolab\Models\Leaderboards\Database\Blacklist;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Entries;
use \Modules\Necrolab\Models\Leaderboards\Database\Snapshots;
use \Modules\Necrolab\Models\Leaderboards\Database\Entry;
use \Modules\Necrolab\Models\Leaderboards\Database\Replays;
use \Modules\Necrolab\Models\Leaderboards\Database\Details;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers;
use \Modules\Necrolab\Models\Leaderboards\RecordModels\Leaderboard;
use \Modules\Necrolab\Models\Leaderboards\RecordModels\LeaderboardEntry;
use \Modules\Necrolab\Models\Leaderboards\RecordModels\SteamReplay;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\Leaderboard as DatabaseLeaderboard;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\LeaderboardEntry as DatabaseLeaderboardEntry;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\Steamreplay as DatabaseSteamReplay;
use \Modules\Necrolab\Models\SteamUsers\RecordModels\SteamUser;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;

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
        
        $xml_file_groups = Leaderboards::getXmlFiles($this->as_of_date);
        
        $leaderboards_xml = Leaderboards::getXml($xml_file_groups['leaderboards_xml']);
        $parsed_xml = Leaderboards::getParsedXml($leaderboards_xml);
        
        unset($xml_file_groups['leaderboards_xml']);
        unset($leaderboards_xml);
        
        if(!empty($parsed_xml->leaderboard)) {
            db()->beginTransaction();
        
            foreach($parsed_xml->leaderboard as $leaderboard) {
                $leaderboard_record = new Leaderboard();
                
                $leaderboard_record->setPropertiesFromObject($leaderboard);
                
                $lbid = $leaderboard_record->lbid;
                
                if($leaderboard_record->isValid($this->as_of_date)) {
                    $database_leaderboard = new DatabaseLeaderboard();
                    
                    $database_leaderboard->setPropertiesFromArray($leaderboard_record->toArray(false));
                
                    $leaderboard_id = Leaderboards::save($database_leaderboard);
                    
                    $database_leaderboard->leaderboard_id = $leaderboard_id;
                    
                    if(!empty($xml_file_groups[$lbid])) {
                        $xml_file_group = $xml_file_groups[$lbid];
                        
                        $leaderboard_snapshot_id = Snapshots::save($database_leaderboard, $this->as_of_date);
                        
                        Entries::clear($leaderboard_snapshot_id, $this->as_of_date);
                        
                        foreach($xml_file_group as $page => $xml_file) {
                            $entries_xml = Leaderboards::getXml($xml_file);
                            
                            $parsed_xml = Leaderboards::getParsedXml($entries_xml);
                            
                            unset($entries_xml);
                            
                            if(!empty($parsed_xml->entries->entry)) {
                                $rank = 1;
                            
                                foreach($parsed_xml->entries->entry as $entry) {
                                    $entry_record = new LeaderboardEntry();
                                    
                                    $entry_record->setPropertiesFromSteamObject($entry, $leaderboard_record);
                                    
                                    $steam_user_id = SteamUsers::getId($entry->steamid);
                                    
                                    if(empty($steam_user_id)) {
                                        $database_steam_user = new DatabaseSteamUser();
                                        
                                        $database_steam_user->steamid = $entry->steamid;
                                    
                                        $steam_user_id = SteamUsers::save($database_steam_user, 'steam_import');
                                    }
                                    
                                    if($entry_record->isValid($leaderboard_record)) {
                                        $steam_replay_id = Replays::save($entry_record->ugcid, $steam_user_id);
                                        $leaderboard_entry_details_id = Details::save($entry_record->details);
                                    
                                        $database_entry = new DatabaseLeaderboardEntry();
                                        
                                        $database_entry->setPropertiesFromArray($entry_record->toArray(false));
                                        
                                        $database_entry->leaderboard_snapshot_id = $leaderboard_snapshot_id;
                                        $database_entry->steam_user_id = $steam_user_id;
                                        $database_entry->steam_replay_id = $steam_replay_id;
                                        $database_entry->leaderboard_entry_details_id = $leaderboard_entry_details_id;
                                        
                                        Entry::save($this->as_of_date, $database_entry);                                        
                                    
                                        $rank += 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            db()->commit();
        }
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
    }
}