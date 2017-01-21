<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Deathless;

use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Rankings\Cache\Rankings as BaseRankings;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames;

class Rankings
extends BaseRankings {    
    public static function getLatestRankingsResultset() {
        $cache = cache('read');
    
        $resultset = new Redis(CacheNames::getDeathlessRankingName(), $cache);
        
        $resultset->setEntriesName(CacheNames::getDeathlessEntriesName());
        $resultset->setFilterName(CacheNames::getDeathlessEntriesFilterName()); 
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processCategoryResultset('deathless_score', __NAMESPACE__ . '\RecordModels\DeathlessEntry', $result_data);
        });
        
        return $resultset;
    }
}