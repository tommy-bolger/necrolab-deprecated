<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Modules\Necrolab\Models\Leaderboards\Entry as BaseEntry;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\LeaderboardEntry as DatabaseEntry;
use \Modules\Necrolab\Models\Releases\Database\Releases as DatabaseReleases;

class Entry
extends BaseEntry {
    public static function save(DateTime $date, array $database_entry, $cache_query_name = NULL) {    
        db()->insert("leaderboard_entries_{$date->format('Y_m')}", $database_entry, $cache_query_name, false);
    }
    
    public static function getIfWin(DateTime $date, $release_id, $zone, $level) {    
        $is_win = 0;
        
        $release = DatabaseReleases::getByDateAndId($date, $release_id);
        
        if(!empty($release)) {                    
            if($zone == $release['win_zone'] && $level == $release['win_level']) {
                $is_win = 1;
            }
        }
        
        return $is_win;
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            )
        ));
    }
}