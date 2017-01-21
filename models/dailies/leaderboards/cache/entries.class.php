<?php
namespace Modules\Necrolab\Models\Dailies\Leaderboards\Cache;

use \DateTime;
use \Modules\Necrolab\Models\Dailies\Leaderboards\Entries as BaseEntries;
use \Modules\Necrolab\Models\DailyRankings\Database\DailyRankings as DatabaseDailyRankings;
use \Modules\Necrolab\Models\DailyRankings\Cache\RecordModels\DailyRankingEntry;
use \Modules\Necrolab\Models\Leaderboards\Cache\Leaderboards;

class Entries
extends BaseEntries {    
    public function save(DateTime $date, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
    
        $entries_name = CacheNames::getLeaderboardEntriesName($this->lbid);
        $date_index = $date->format('ymd')
    
        $cache->hDel($entries_name, $date_index);
        
        $cache->hSet($entries_name, $date_index, gzcompress(json_encode($this->entries), 9));
    }

    /*public static function save($lbid, $max_rank, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        Leaderboards::saveEntries($lbid, $max_rank, CacheNames::getRankingsBaseName(), $cache);
    }*/
    
    public static function loadIntoCache($cache = NULL) {    
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $leaderboards_resultset = DatabaseDailyRankings::getLeaderboards();
        
        $leaderboards = $leaderboards_resultset->getAssoc();
        
        $transaction = $cache->transaction();

        $resultset = DatabaseDailyRankings::getLeaderboardEntries();
        
        $max_ranks = array();
        $daily_entries = $resultset->prepareExecuteQuery();

        while($daily_entry = $daily_entries->fetch(PDO::FETCH_ASSOC)) {     
            $lbid = $daily_entry['lbid'];
            
            $leaderboard_record = new Leaderboard();
            $leaderboard_record->setPropertiesFromArray($leaderboards[$lbid][0]);
            $leaderboard_record->lbid = $lbid;
        
            static::saveDailyLeaderboardEntry($daily_entry, $leaderboard_record, $transaction);
            
            $max_ranks[$lbid] = $daily_entry['rank'];
        }
        
        if(!empty($max_ranks)) {
            foreach($max_ranks as $lbid => $max_rank) {
                static::saveDailyLeaderboardEntries($lbid, $max_rank, $transaction);
            }
        }

        $transaction->commit();
    }
}