<?php
namespace Modules\Necrolab\Controllers\Cli\Rankings;

use \DateTime;
use \DateInterval;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Necrolab;
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
    
    public function init() {
        $this->cache = cache();
    }
    
    public function actionGenerate($date = NULL) {
        $date = new DateTime($date);
        
        $this->as_of_date = $date;
        
        $day_types = DatabaseDayTypes::getActiveForDate($date);
        
        $current_date = new DateTime($this->module->configuration->steam_live_launch_date);
        //$current_date = new DateTime('2016-01-01');
        
        $transaction = $this->cache->transaction();
        
        while($current_date <= $date) {        
            $leaderboard_entries_resulset = DatabaseLeaderboardEntries::getDailyRankingsResultset($current_date);
        
            $leaderboard_entries = $leaderboard_entries_resulset->prepareExecuteQuery();
            
            $database = db();
            
            while($leaderboard_entry = $database->getStatementRow($leaderboard_entries)) {
                foreach($day_types as $day_type) {
                    if($current_date >= $day_type['start_date']) {
                        CacheDailyRankingEntry::saveFromLeaderboardEntry($leaderboard_entry, $day_type['daily_ranking_day_type_id'], $transaction);
                    }
                }
            }
        
            $current_date->add(new DateInterval('P1D'));
        }

        $transaction->commit();        
        
        //First pass to assign ranks to entries
        foreach($day_types as $day_type) {
            CacheDailyRankings::generateRanksFromPoints($day_type['daily_ranking_day_type_id'], $this->cache);
        }
        
        //Second pass to save finalized entries into database
        db()->beginTransaction();
        
        foreach($day_types as $day_type) {
            $daily_ranking_id = DatabaseDailyRankings::save($day_type['daily_ranking_day_type_id'], $this->as_of_date);
            
            DatabaseDailyRankingEntries::clear($daily_ranking_id, $date);
        
            CacheDailyRankingEntries::saveToDatabase($daily_ranking_id, $day_type['daily_ranking_day_type_id'], $date, $this->cache);
        }
        
        db()->commit();
    }
}