<?php
namespace Modules\Necrolab\Models\Dailies\Seasons\Leaderboards\Cache;

use \DateTime;
use \Modules\Necrolab\Models\Dailies\Seasons\Entries as BaseEntries;

class Entries
extends BaseEntries {
    public function save(DateTime $date, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
    
        $entries_name = CacheNames::getLeaderboardEntriesName($this->lbid);
        $date_index = $date->format('ymd')
    
        $cache->hDel($entries_name, $date_index);
        
        $cache->hSet($entries_name, $date_index, gzcompress(json_encode($this->entries), 9));
    }

    /*public static function save($season_number, $lbid, $max_rank, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        Leaderboards::saveEntries($lbid, $max_rank, CacheNames::getRankingName($season_number), $cache);
    }*/
}