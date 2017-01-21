<?php
namespace Modules\Necrolab\Controllers\Cli\SteamImport;

use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Framework\Utilities\ParallelProcessQueue;
use \Modules\Necrolab\Models\Leaderboards\Blacklist;
use \Modules\Necrolab\Models\Leaderboards\Leaderboards;
use \Modules\Necrolab\Models\Leaderboards\RecordModels\Leaderboard;

class BaseProcess
extends Cli {
    protected $as_of_date;
    protected $imported_leaderboards = array();    
    
    protected function getBlacklistRecord($lbid) {}
    
    protected function leaderboardIsValid(Leaderboard $leaderboard) {
        $daily_date_difference = NULL;
        
        if($leaderboard->is_daily == 1 && $leaderboard->is_daily_ranking) {
            $daily_date_difference = $this->as_of_date->diff($leaderboard->daily_date_object);
        }
        
        $blacklist_record = $this->getBlacklistRecord($leaderboard->lbid);
    
        return (
            empty($blacklist_record) && 
            $leaderboard->is_prod == 1 && 
            ($leaderboard->is_daily == 0) || ($leaderboard->is_daily == 1 && $leaderboard->is_daily_ranking == 1 && $leaderboard->daily_date_object >= $this->as_of_date && $daily_date_difference->format('%a') == 0)
        );
    }
    
    protected function saveImportedLeaderboards() {}
    
    protected function importLeaderboards() {
        File::deleteDirectoryRecursive($this->as_of_date);
    
        $leaderboards_xml = Leaderboards::getPLeaderboards::getSteamXml($this->module->configuration->leaderboard_url);
    
        $leaderboards = Leaderboards::getParsedXml($leaderboards_xml);
        
        if(!empty($leaderboards->leaderboard)) {        
            foreach($leaderboards->leaderboard as &$leaderboard) {                
                $leaderboard_record = new Leaderboard();
                
                $leaderboard_record->setPropertiesFromObject($leaderboard);
                
                if($this->leaderboardIsValid($leaderboard_record)) {
                    $this->imported_leaderboards[$leaderboard->lbid] = $leaderboard_record;
                }
            }
            
            $this->saveImportedLeaderboards();
        }
    }
    
    protected function getEntriesTransaction() {
        return NULL;
    }
    
    protected function importPageEntries($imported_leaderboard, $leaderboard_users, &$max_rank, &$transaction) {}
    
    protected function commitEntriesTransaction($transaction) {}
    
    protected function importEntriesChildPostProcess($transaction, $leaderboard_record, $max_rank) {}
    
    public function importEntriesChildProcess($imported_leaderboard) {     
        $transaction = $this->getEntriesTransaction();
    
        $next_page_url = $imported_leaderboard->url;
        
        $max_rank = 0;

        do {
            $retrieval_attempts = 1;
            $retrieval_successful = false;
            $leaderboard_users = NULL;
            
            while($retrieval_successful == false && $retrieval_attempts <= 3) {
                try {
                    $leaderboard_users = Leaderboards::getSteamXml($next_page_url);

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
                throw new Exception("Retrieval for lbid {$imported_leaderboard->lbid} has failed.");
            }
            
            if(!empty($leaderboard_users)) {
                static::importPageEntries($imported_leaderboard, $leaderboard_users, $max_rank, $transaction);
                
                if(!empty($leaderboard_users->nextRequestURL)) {                    
                    $next_page_url = trim($leaderboard_users->nextRequestURL);
                }
                else {
                    $next_page_url = NULL;
                }
            }
        }
        while(!empty($next_page_url));
        
        static::importEntriesChildPostProcess($transaction, $imported_leaderboard, $max_rank);
        
        if(isset($transaction)) {
            static::commitEntriesTransaction($transaction);
        }
    }
    
    protected function importLeaderboardEntries() {
        if(!empty($this->imported_leaderboards)) {
            $leaderboard_import_job_queue = new ParallelProcessQueue();
            
            $leaderboard_import_job_queue->setMaxParallelProcesses(50);
        
            foreach($this->imported_leaderboards as $imported_leaderboard) {
                if($imported_leaderboard->entries > 0) {                    
                    $leaderboard_import_job_queue->addProcessToQueue(array($this, 'importEntriesChildProcess'), array(
                        'imported_leaderboard' => $imported_leaderboard
                    ));
                }
            }
            
            $leaderboard_import_job_queue->run();
        }
    }
    
    public function actionImportByDate($date = NULL) {        
        $this->as_of_date = new DateTime($date);
        
        $this->importLeaderboards();
        
        exit;
        
        $this->importLeaderboardEntries();
    }
}