<?php
namespace Modules\Necrolab\Models\Leaderboards\Cache;

use \DateTime;
use \Framework\Data\ResultSet\Redis\ListStructure as Redis;
use \Modules\Necrolab\Models\Leaderboards\Entries as BaseEntries;
use \Modules\Necrolab\Models\Leaderboards\Cache\CacheNames;

class Entries
extends BaseEntries {
    public static function getRankingsResultset($lbid, $leaderboard_type) {
        $cache = cache('read');
    
        $resultset = new Redis(CacheNames::getLeaderboardName($lbid), $cache);
        
        $resultset->setEntriesName(CacheNames::getEntriesName($lbid));
        
        $resultset->setEntryNameCallback(array(
            CacheNames::getFullClassName(),
            'getEntryName'
        ), array($lbid));
        
        $resultset->setRowsPerPage(100);
        
        switch($leaderboard_type) {
            case 'score':
                $resultset->addProcessorFunction(function($result_data) {
                    return static::processRankingsResultSet('score', $result_data);
                });
                break;
            case 'speed':
                $resultset->addProcessorFunction(function($result_data) {
                    return static::processRankingsResultSet('speed', $result_data);
                });
                break;
            case 'deathless':
                $resultset->addProcessorFunction(function($result_data) {
                    return static::processRankingsResultSet('deathless', $result_data);
                });
                break;
        }
        
        
        return $resultset;
    }
    
    public static function getSteamUsersFromResultData(array $result_data) {
        $steamids = array();

        if(!empty($result_data)) {
            foreach($result_data as $result_row) {
                $steamids[] = $result_row['steamid'];
            }
        }
        
        return SteamUsers::getSocialMediaData($steamids);
    }
    
    public function save(DateTime $date, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
    
        $entries_name = CacheNames::getEntriesName($this->lbid);
        $date_index = $date->format('ymd')
    
        $cache->hDel($entries_name, $date_index);
        
        $cache->hSet($entries_name, $date_index, gzcompress(json_encode($this->entries), 9));
    }
    
    /*public static function save($lbid, $max_rank, $prefix_name = '', $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        if(!empty($prefix_name)) {
            $prefix_name .= ':';
        }
        
        $entries_hash_name = $prefix_name . CacheNames::getEntriesName($lbid);
        
        $cache->set($entries_hash_name, $max_rank);
    }*/
}