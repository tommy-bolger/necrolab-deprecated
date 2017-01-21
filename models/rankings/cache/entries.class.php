<?php
namespace Modules\Necrolab\Models\Rankings\Cache;

use \DateTime;
use \Modules\Necrolab\Models\Rankings\Cache\Power\Rankings as CachePowerRankings;
use \Modules\Necrolab\Models\Rankings\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\Rankings\Database\RecordModels\PowerRankingEntry as DatabasePowerRankingEntry;

class Entries {
    public static function saveChunkToDatabase($entries, $power_ranking_id, $date) {
        if(!empty($entries)) {
            foreach($entries as $entry) {
                if(!empty($entry)) {
                    $power_ranking_entry = new DatabasePowerRankingEntry();
                    
                    $power_ranking_entry->setPropertiesFromArray($entry);
                    
                    $power_ranking_entry->power_ranking_id = $power_ranking_id;
                    
                    DatabaseEntry::save($date, $power_ranking_entry);
                }
            }
        }
    }

    public static function saveToDatabase($power_ranking_id, DateTime $date, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache();
        }
    
        $power_ranking_entries = CachePowerRankings::getTotalPointsByRank($date);
    
        $transaction = $cache->transaction();
            
        $transaction->setCommitProcessCallback(array(get_called_class(), 'saveChunkToDatabase'), array(
            'power_ranking_id' => $power_ranking_id,
            'date' => $date
        ));
    
        foreach($power_ranking_entries as $rank => $steam_user_id) {
            $transaction->hGetAll(CacheNames::getPowerRankingEntryName($steam_user_id));
        }
        
        $transaction->commit();
    }
}