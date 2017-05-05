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
    public static function saveChunkToDatabase($entries, $daily_ranking_id, $date, $entries_insert_queue) {
        if(!empty($entries)) {
            foreach($entries as $entry) {
                if(!empty($entry)) {                
                    $daily_ranking_entry = new DatabaseDailyRankingEntry();
                    
                    $daily_ranking_entry->setPropertiesFromArray($entry);
                    
                    $daily_ranking_entry->daily_ranking_id = $daily_ranking_id;
                    
                    $entries_insert_queue->addRecord($daily_ranking_entry->toArray());
                }
            }
        }
    }

    public static function saveToDatabase($daily_ranking_id, $release_id, $mode_id, $daily_ranking_day_type_id, DateTime $date, $cache, $entries_insert_queue) {
        $daily_ranking_entries = CacheDailyRankings::getTotalPointsByRank($release_id, $mode_id, $daily_ranking_day_type_id, $cache);
    
        $transaction = $cache->transaction();
            
        $transaction->setCommitProcessCallback(array(get_called_class(), 'saveChunkToDatabase'), array(
            'daily_ranking_id' => $daily_ranking_id,
            'date' => $date,
            'entries_insert_queue' => $entries_insert_queue
        ));
    
        foreach($daily_ranking_entries as $rank => $steam_user_id) {
            $transaction->hGetAll(CacheNames::getEntryName($release_id, $mode_id, $daily_ranking_day_type_id, $steam_user_id));
        }
        
        $transaction->commit();
    }
}