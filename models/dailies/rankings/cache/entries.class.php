<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Cache;

use \DateTime;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Dailies\Rankings\Entries as BaseEntries;
use \Modules\Necrolab\Models\Dailies\Rankings\Cache\Rankings as CacheDailyRankings;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\RecordModels\DailyRankingEntry as DatabaseDailyRankingEntry;

class Entries
extends BaseEntries {     
    public static function saveChunkToDatabase($entries, $daily_ranking_id, $daily_ranking_day_type_id, $date) {
        if(!empty($entries)) {
            foreach($entries as $entry) {
                if(!empty($entry)) {
                    //Generate stats
                    $total_points = $entry['total_points'];
                    $total_dailies = $entry['total_dailies'];
                    $sum_of_ranks = $entry['sum_of_ranks'];
                    
                    $entry['points_per_day'] = $total_points / $total_dailies;
                    $entry['average_rank'] = $sum_of_ranks / $total_dailies;
                
                    //Save record
                    $daily_ranking_entry = new DatabaseDailyRankingEntry();
                    
                    $daily_ranking_entry->setPropertiesFromArray($entry);
                    
                    $daily_ranking_entry->daily_ranking_id = $daily_ranking_id;
                    
                    DatabaseEntry::save($date, $daily_ranking_entry);
                }
            }
        }
    }

    public static function saveToDatabase($daily_ranking_id, $daily_ranking_day_type_id, DateTime $date, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache();
        }
    
        $daily_ranking_entries = CacheDailyRankings::getTotalPointsByRank($daily_ranking_day_type_id, $cache);
    
        $transaction = $cache->transaction();
            
        $transaction->setCommitProcessCallback(array(get_called_class(), 'saveChunkToDatabase'), array(
            'daily_ranking_id' => $daily_ranking_id,
            'daily_ranking_day_type_id' => $daily_ranking_day_type_id,
            'date' => $date
        ));
    
        foreach($daily_ranking_entries as $rank => $steam_user_id) {
            $transaction->hGetAll(CacheNames::getEntryName($steam_user_id, $daily_ranking_day_type_id));
        }
        
        $transaction->commit();
    }
}