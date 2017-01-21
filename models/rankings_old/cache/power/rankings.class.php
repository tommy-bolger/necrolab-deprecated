<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Power;

use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Rankings\Cache\Rankings as BaseRankings;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames as RankingCacheNames;

class Rankings
extends BaseRankings {
    public static function getLatestRankingsResultset() {
        $cache = cache('read');
    
        $resultset = new Redis(RankingCacheNames::getPowerRankingName(), $cache);
        
        $resultset->setEntriesName(RankingCacheNames::getPowerEntriesName());
        $resultset->setFilterName(RankingCacheNames::getPowerEntriesFilterName());  
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processPowerResultset(__NAMESPACE__ . '\RecordModels\PowerRankingEntry', $result_data);
        });
        
        return $resultset;
    }
}