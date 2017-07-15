<?php
namespace Modules\Necrolab\Controllers\Cli\Rankings;

use \DateTime;
use \DateInterval;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Characters;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\Releases;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Entries as DatabaseLeaderboardEntries;
use \Modules\Necrolab\Models\Rankings\Database\Rankings as DatabaseRankings;
use \Modules\Necrolab\Models\Rankings\Database\Entries as DatabaseEntries;
use \Modules\Necrolab\Models\Rankings\Cache\Entries as CacheEntries;
use \Modules\Necrolab\Models\Rankings\Cache\Entry as CacheEntry;
use \Modules\Necrolab\Models\Rankings\Cache\Power\Rankings as CachePowerRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Speed\Rankings as CacheSpeedRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Score\Rankings as CacheScoreRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Deathless\Rankings as CacheDeathlessRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Character\Rankings as CacheCharacterRankings;
use \Modules\Necrolab\Models\Rankings\Database\RecordModels\PowerRankingEntry;

class Power
extends Cli { 
    protected $cache;
    
    protected $as_of_date;
    
    public function init() {
        $this->cache = cache('power_rankings');
    }
    
    protected function generate() {
        $this->cache->clear();
        
        $database = db();
    
        $leaderboard_entries_resulset = DatabaseLeaderboardEntries::getPowerRankingsResultset($this->as_of_date);
        
        $leaderboard_entries_resulset->setAsCursor(10000);

        $database->beginTransaction();
        
        $leaderboard_entries_resulset->prepareExecuteQuery();

        $transaction = $this->cache->transaction();
        
        $leaderboard_entries = array();
        
        do {
            $leaderboard_entries = $leaderboard_entries_resulset->getNextCursorChunk();
        
            if(!empty($leaderboard_entries)) {
                foreach($leaderboard_entries as $leaderboard_entry) {        
                    CacheEntry::saveFromLeaderboardEntry($leaderboard_entry, $transaction);
                }
            }
        }
        while(!empty($leaderboard_entries));
        
        $transaction->commit();   
        
        $database->commit();
        
        $releases = Releases::getByDate($this->as_of_date);
        $modes = Modes::getAll();
        
        if(!empty($releases)) {
            $characters = Characters::getActive();
        
            $database->beginTransaction();
            
            DatabaseEntries::createTemporaryTable();
            
            $entries_insert_queue = DatabaseEntries::getTempInsertQueue();
            
            $seeded_flags = array(0, 1);
        
            foreach($releases as $release) {
                $release_id = $release['release_id'];
                
                foreach($seeded_flags as $seeded) {
                    $modes_used = CachePowerRankings::getModesUsed($release_id, $seeded, $this->cache);

                    if(!empty($modes_used)) {
                        foreach($modes_used as $mode_id) {                        
                            $mode = Modes::getById($mode_id);
                            
                            if(!empty($mode)) {
                                CacheSpeedRankings::generateRanksFromPoints($this->as_of_date, $release_id, $mode_id, $seeded, $this->cache);
                                CacheScoreRankings::generateRanksFromPoints($this->as_of_date, $release_id, $mode_id, $seeded, $this->cache);
                                CacheDeathlessRankings::generateRanksFromPoints($this->as_of_date, $release_id, $mode_id, $seeded, $this->cache);

                                if(!empty($characters)) {
                                    foreach($characters as $character) {
                                        CacheCharacterRankings::generateRanksFromPoints($this->as_of_date, $release_id, $mode_id, $seeded, $character['name'], $this->cache);
                                    }
                                }

                                CachePowerRankings::generateRanksFromPoints($this->as_of_date, $release_id, $mode_id, $seeded, $this->cache);
                                
                                $power_ranking_id = DatabaseRankings::save($release_id, $mode_id, $seeded, $this->as_of_date);
                                
                                DatabaseEntries::clear($power_ranking_id, $this->as_of_date);
                            
                                CacheEntries::saveToDatabase($power_ranking_id, $release_id, $mode_id, $seeded, $this->as_of_date, $this->cache, $entries_insert_queue);
                            }
                        }
                    }
                }
            }
            
            $entries_insert_queue->commit();
            
            DatabaseEntries::dropPartitionTableConstraints($this->as_of_date);
            DatabaseEntries::dropPartitionTableIndexes($this->as_of_date);
            
            DatabaseEntries::saveTempEntries($this->as_of_date);
            
            DatabaseEntries::createPartitionTableConstraints($this->as_of_date);
            DatabaseEntries::createPartitionTableIndexes($this->as_of_date);
            
            $database->commit();
        }
        
        $this->cache->clear();
        
        DatabaseRankings::vacuum();
        DatabaseEntries::vacuum($this->as_of_date);
        
        DatabaseRankings::addToCacheQueue($this->as_of_date);
    }
    
    public function actionGenerate($date = NULL) {
        $this->as_of_date = new DateTime($date);

        $this->generate();
    }
    
    public function actionGenerateRange($start_date, $end_date) {        
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            $this->actionGenerate($current_date->format('Y-m-d'));
        
            $current_date->add(new DateInterval('P1D'));
        }
    }
    
    public function generateQueueMessageReceived($message) {
        $this->actionGenerate($message->body);
    }
    
    public function actionRunGenerateQueueListener() {    
        DatabaseRankings::runQueue(DatabaseRankings::getGenerateQueueName(), array(
            $this,
            'generateQueueMessageReceived'
        ));
    }
    
    public function actionLoadIntoCache($date = NULL) {        
        DatabaseEntries::loadIntoCache(new DateTime($date));
    }
    
    public function actionLoadRangeIntoCache($start_date, $end_date) {        
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            $this->actionLoadIntoCache($current_date->format('Y-m-d'));
        
            $current_date->add(new DateInterval('P1D'));
        }
    }
    
    public function cacheQueueMessageReceived($message) {
        $this->actionLoadIntoCache($message->body);
    }
    
    public function actionRunCacheQueueListener() {    
        DatabaseRankings::runQueue(DatabaseRankings::getCacheQueueName(), array(
            $this,
            'cacheQueueMessageReceived'
        ));
    }
    
    public function actionCreateEntriesParition($date = NULL) {
        $date = new DateTime($date);
    
        DatabaseEntries::createPartitionTable($date);
    }
    
    public function actionCreateNextMonthEntriesPartition($date = NULL) {
        $date = new DateTime($date);
        
        $date->add(new DateInterval('P1M'));
        
        DatabaseEntries::createPartitionTable($date);
    }
    
    public function actionCreateEntriesParitions($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
    
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            DatabaseEntries::createPartitionTable($current_date);
        
            $current_date->add(new DateInterval('P1M'));
        }
    }
}