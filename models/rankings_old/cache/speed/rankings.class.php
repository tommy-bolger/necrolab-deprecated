<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Speed;

use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Rankings\Cache\Rankings as BaseRankings;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames;

class Rankings
extends BaseRankings {    
    public static function getLatestRankingsResultset() {
        $cache = cache('read');
    
        $resultset = new Redis(CacheNames::getSpeedRankingName(), $cache);
        
        $resultset->setEntriesName(CacheNames::getSpeedEntriesName());
        $resultset->setFilterName(CacheNames::getSpeedEntriesFilterName());   
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processCategoryResultset('speed', __NAMESPACE__ . '\RecordModels\SpeedEntry', $result_data);
        });
        
        return $resultset;
    }
}