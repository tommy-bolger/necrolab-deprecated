<?php
namespace Modules\Necrolab\Controllers\Cli\Rankings;

use \DateTime;
use \DateInterval;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\Releases;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\Leaderboards\Database\Entries as DatabaseLeaderboardEntries;
use \Modules\Necrolab\Models\Dailies\Rankings\DayTypes;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\Rankings as DatabaseDailyRankings;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\Entries as DatabaseDailyRankingEntries;
use \Modules\Necrolab\Models\Dailies\Rankings\Cache\Rankings as CacheDailyRankings;
use \Modules\Necrolab\Models\Dailies\Rankings\Cache\Entries as CacheDailyRankingEntries;
use \Modules\Necrolab\Models\Dailies\Rankings\Cache\Entry as CacheDailyRankingEntry;

class Dailies
extends Cli { 
    protected $cache;
    
    protected $as_of_date;
    
    protected $release;
    
    public function init() {
        $this->cache = cache('daily_rankings');
    }
    
    protected function generate() {
        $this->cache->clear();
        
        $release_id = $this->release['release_id'];
    
        $day_types = DayTypes::getActiveForDate($this->as_of_date);
        
        $transaction = $this->cache->transaction();
        
        $leaderboard_entries_resulset = DatabaseLeaderboardEntries::getDailyRankingsResultset($this->as_of_date, $this->release);
        
        $leaderboard_entries = $leaderboard_entries_resulset->prepareExecuteQuery();
            
        $database = db();
        
        while($leaderboard_entry = $database->getStatementRow($leaderboard_entries)) {
            foreach($day_types as $day_type) {
                $current_date = new DateTime($leaderboard_entry['date']);
            
                if($current_date >= $day_type['start_date']) {
                    CacheDailyRankingEntry::saveFromLeaderboardEntry($leaderboard_entry, $day_type['daily_ranking_day_type_id'], $transaction);
                }
            }
        }
        
        $transaction->commit(); 
        
        $modes_used = CacheDailyRankings::getModesUsed($release_id, $this->cache);
        
        if(!empty($modes_used)) {
            $database->beginTransaction();
            
            DatabaseDailyRankingEntries::createTemporaryTable();
            
            $entries_insert_queue = DatabaseDailyRankingEntries::getTempInsertQueue();
        
            foreach($modes_used as $mode_id) {
                $mode = Modes::getById($mode_id);
                
                if(!empty($mode)) {
                    $daily_ranking_day_types_used = CacheDailyRankings::getNumberOfDaysModesUsed($release_id, $mode_id, $this->cache);
                
                    if(!empty($daily_ranking_day_types_used)) {
                        foreach($daily_ranking_day_types_used as $daily_ranking_day_type_id) {
                            if(isset($day_types[$daily_ranking_day_type_id])) {
                                $day_type = $day_types[$daily_ranking_day_type_id];
                                
                                CacheDailyRankings::generateRanksFromPoints($release_id, $mode_id, $daily_ranking_day_type_id, $this->cache);
                    
                                $daily_ranking_id = DatabaseDailyRankings::save($release_id, $mode_id, $daily_ranking_day_type_id, $this->as_of_date);
                                
                                DatabaseDailyRankingEntries::clear($daily_ranking_id, $this->as_of_date);
                            
                                CacheDailyRankingEntries::saveToDatabase($daily_ranking_id, $release_id, $mode_id, $daily_ranking_day_type_id, $this->as_of_date, $this->cache, $entries_insert_queue);
                            }
                        }
                    }
                }
            }
            
            $entries_insert_queue->commit();
            
            DatabaseDailyRankingEntries::dropPartitionTableConstraints($this->as_of_date);
            DatabaseDailyRankingEntries::dropPartitionTableIndexes($this->as_of_date);
            
            DatabaseDailyRankingEntries::saveTempEntries($this->as_of_date);
            
            DatabaseDailyRankingEntries::createPartitionTableConstraints($this->as_of_date);
            DatabaseDailyRankingEntries::createPartitionTableIndexes($this->as_of_date);
            
            $database->commit();
        }
        
        $this->cache->clear();
        
        DatabaseDailyRankings::vacuum();
        DatabaseDailyRankingEntries::vacuum($this->as_of_date);
    }
    
    public function actionGenerate($date = NULL) {        
        $this->as_of_date = new DateTime($date);
    
        $releases = Releases::getByDate($this->as_of_date);
        
        if(!empty($releases)) {
            foreach($releases as $release) {
                $this->release = $release;
                
                $this->generate();
            }
            
            DatabaseDailyRankings::addToCacheQueue($this->as_of_date);
        }
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
        DatabaseDailyRankings::runQueue(DatabaseDailyRankings::getGenerateQueueName(), array(
            $this,
            'generateQueueMessageReceived'
        ));
    }
    
    public function actionLoadIntoCache($date = NULL) {        
        DatabaseDailyRankingEntries::loadIntoCache(new DateTime($date));
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
        DatabaseDailyRankings::runQueue(DatabaseDailyRankings::getCacheQueueName(), array(
            $this,
            'cacheQueueMessageReceived'
        ));
    }        
    
    public function actionCreateEntriesParition($date = NULL) {
        $date = new DateTime($date);
    
        DatabaseDailyRankingEntries::createPartitionTable($date);
    }
    
    public function actionCreateNextMonthEntriesPartition($date = NULL) {
        $date = new DateTime($date);
        
        $date->add(new DateInterval('P1M'));
        
        DatabaseDailyRankingEntries::createPartitionTable($date);
    }
    
    public function actionCreateEntriesParitions($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
    
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            DatabaseDailyRankingEntries::createPartitionTable($current_date);
        
            $current_date->add(new DateInterval('P1M'));
        }
    }
}