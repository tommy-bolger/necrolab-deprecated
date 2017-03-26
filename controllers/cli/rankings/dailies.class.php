<?php
namespace Modules\Necrolab\Controllers\Cli\Rankings;

use \DateTime;
use \DateInterval;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\Releases\Database\Releases as DatabaseReleases;
use \Modules\Necrolab\Models\Modes\Database\Modes as DatabaseModes;
use \Modules\Necrolab\Models\Leaderboards\Database\Entries as DatabaseLeaderboardEntries;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\DayTypes as DatabaseDayTypes;
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
    
        $day_types = DatabaseDayTypes::getActiveForDate($this->as_of_date);
        
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
        
            foreach($modes_used as $mode_id) {
                $mode = DatabaseModes::getById($mode_id);
                
                if(!empty($mode)) {
                    $daily_ranking_day_types_used = CacheDailyRankings::getNumberOfDaysModesUsed($release_id, $mode_id, $this->cache);
                
                    if(!empty($daily_ranking_day_types_used)) {
                        foreach($daily_ranking_day_types_used as $daily_ranking_day_type_id) {
                            if(isset($day_types[$daily_ranking_day_type_id])) {
                                $day_type = $day_types[$daily_ranking_day_type_id];
                                
                                CacheDailyRankings::generateRanksFromPoints($release_id, $mode_id, $daily_ranking_day_type_id, $this->cache);
                    
                                $daily_ranking_id = DatabaseDailyRankings::save($release_id, $mode_id, $daily_ranking_day_type_id, $this->as_of_date);
                                
                                DatabaseDailyRankingEntries::clear($daily_ranking_id, $this->as_of_date);
                            
                                CacheDailyRankingEntries::saveToDatabase($daily_ranking_id, $release_id, $mode_id, $daily_ranking_day_type_id, $this->as_of_date, $this->cache);
                            }
                        }
                    }
                }
            }
            
            $database->commit();
        }
        
        $this->cache->clear();
    }
    
    public function actionGenerate($date = NULL) {        
        $this->as_of_date = new DateTime($date);
    
        $releases = DatabaseReleases::getByDate($this->as_of_date);
        
        if(!empty($releases)) {
            foreach($releases as $release) {
                $this->release = $release;
                
                $this->generate();
            }
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