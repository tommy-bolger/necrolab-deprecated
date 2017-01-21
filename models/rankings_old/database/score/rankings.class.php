<?php
namespace Modules\Necrolab\Models\Rankings\Cache\Score;

use \Framework\Data\ResultSet\Redis;
use \Modules\Necrolab\Models\Rankings\Cache\Rankings as BaseRankings;

class Rankings
extends BaseRankings {    
    public static function getLatestRankingsResultset() {
        $cache = cache('read');
    
        $resultset = new Redis(CacheNames::getScoreRankingName(), $cache);
        
        $resultset->setEntriesName(CacheNames::getScoreEntriesName());
        $resultset->setFilterName(CacheNames::getScoreEntriesFilterName());     
        
        $resultset->addProcessorFunction(function($result_data) {
            return static::processResultset('score', __NAMESPACE__ . '\RecordModels\ScoreEntry', $result_data);
        });
        
        return $resultset;
    }
}