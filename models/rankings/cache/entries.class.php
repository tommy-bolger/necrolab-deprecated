<?php
namespace Modules\Necrolab\Models\Rankings\Cache;

use \DateTime;
use \Modules\Necrolab\Models\Rankings\Cache\Power\Rankings as CachePowerRankings;
use \Modules\Necrolab\Models\Rankings\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\Rankings\Database\RecordModels\PowerRankingEntry as DatabasePowerRankingEntry;

class Entries {
    public static function saveChunkToDatabase($entries, $power_ranking_id, DateTime $date, $entries_insert_queue) {
        if(!empty($entries)) {
            foreach($entries as $entry) {
                if(!empty($entry)) {
                    $power_ranking_entry = new DatabasePowerRankingEntry();
                    
                    $power_ranking_entry->setPropertiesFromArray($entry);

                    $power_ranking_entry->power_ranking_id = $power_ranking_id;
                    
                    $entries_insert_queue->addRecord($power_ranking_entry->toArray());
                }
            }
        }
    }

    public static function saveToDatabase($power_ranking_id, $release_id, $mode_id, DateTime $date, $cache, $entries_insert_queue) {
        $power_ranking_entries = CachePowerRankings::getTotalPointsByRank($date, $release_id, $mode_id, $cache);
    
        $transaction = $cache->transaction();
            
        $transaction->setCommitProcessCallback(array(get_called_class(), 'saveChunkToDatabase'), array(
            'power_ranking_id' => $power_ranking_id,
            'date' => $date,
            'entries_insert_queue' => $entries_insert_queue
        ));
    
        foreach($power_ranking_entries as $rank => $steam_user_id) {
            $transaction->hGetAll(CacheNames::getPowerRankingEntryName($release_id, $mode_id, $steam_user_id));
        }
        
        $transaction->commit();
    }
}