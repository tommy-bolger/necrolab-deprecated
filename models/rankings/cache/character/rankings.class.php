<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Character;

use \DateTime;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Rankings\Cache\Rankings as BaseRankings;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames;

class Rankings
extends BaseRankings {
    public static function generateRanksFromPoints(DateTime $date, $character_name, $cache = NULL) {  
        if(empty($cache)) {
            $cache = cache();
        }  
    
        $total_points_entries = static::getTotalPointsByRank($date, $character_name);
        
        $transaction = $cache->transaction();
        
        foreach($total_points_entries as $rank => $steam_user_id) {
            $real_rank = $rank + 1;
            
            $transaction->hSet(CacheNames::getPowerRankingEntryName($steam_user_id), "{$character_name}_rank", $real_rank);
        }
        
        $transaction->commit();
    }

    public static function getTotalPointsByRank(DateTime $date, $character_name, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache();
        }        
        
        return $cache->zRevRange(CacheNames::getCharacterPointsName($character_name), 0, -1);
    }

    public static function getLatestRankingsResultset() {
        $cache = cache('read');
    
        $resultset = new Redis(CacheNames::getCharacterRankingName(), $cache);
        
        $resultset->setEntriesName(CacheNames::getCharacterEntriesName());
        $resultset->setFilterName(CacheNames::getCharacterEntriesFilterName());   
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processCategoryResultset('character', __NAMESPACE__ . '\RecordModels\CharacterEntry', $result_data);
        });
        
        return $resultset;
    }
}