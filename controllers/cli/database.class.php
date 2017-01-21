<?php
namespace Modules\Necrolab\Controllers\Cli;

use \DateTime;
use \DateInterval;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Leaderboards\Database\Entries as LeaderboardEntries;
use \Modules\Necrolab\Models\Rankings\Database\Entries as RankingEntries;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\Entries as DailyRankingEntries;

class Database
extends Cli { 
    public function actionCreateLeaderboardEntriesParition($date = NULL) {
        $date = new DateTime($date);
    
        LeaderboardEntries::createPartitionTable($date);
    }
    
    public function actionCreateNextMonthLeaderboardEntriesPartition($date = NULL) {
        $date = new DateTime($date);
        
        $date->add(new DateInterval('P1M'));
        
        LeaderboardEntries::createPartitionTable($date);
    }
    
    public function actionCreateLeaderboardEntriesParitions($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
    
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            LeaderboardEntries::createPartitionTable($current_date);
        
            $current_date->add(new DateInterval('P1M'));
        }
    }
    
    
    
    public function actionCreateRankingEntriesParition($date = NULL) {
        $date = new DateTime($date);
    
        RankingEntries::createPartitionTable($date);
    }
    
    public function actionCreateNextMonthRankingEntriesPartition($date = NULL) {
        $date = new DateTime($date);
        
        $date->add(new DateInterval('P1M'));
        
        RankingEntries::createPartitionTable($date);
    }
    
    public function actionCreateRankingEntriesParitions($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
    
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            RankingEntries::createPartitionTable($current_date);
        
            $current_date->add(new DateInterval('P1M'));
        }
    }
    
    
    
    public function actionCreateDailyRankingEntriesParition($date = NULL) {
        $date = new DateTime($date);
    
        DailyRankingEntries::createPartitionTable($date);
    }
    
    public function actionCreateNextMonthDailyRankingEntriesPartition($date = NULL) {
        $date = new DateTime($date);
        
        $date->add(new DateInterval('P1M'));
        
        DailyRankingEntries::createPartitionTable($date);
    }
    
    public function actionCreateDailyRankingEntriesParitions($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
    
        $current_date = clone $start_date;
        
        while($current_date <= $end_date) {
            DailyRankingEntries::createPartitionTable($current_date);
        
            $current_date->add(new DateInterval('P1M'));
        }
    }
}