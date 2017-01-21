<?php
namespace Modules\Necrolab\Models\Dailies\Leaderboards\Database;

use \Modules\Necrolab\Models\Dailies\Leaderboards\Leaderboards as BaseLeaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards;

class Leaderboards
extends BaseLeaderboards {    
    public static function getLeaderboards() {
        $resultset = Leaderboards::getLeaderboardsBaseResultset();
        
        $resultset->addFilterCriteria('l.is_daily_ranking = 1');
        $resultset->addSortCriteria('l.daily_date', 'ASC');
        
        return $resultset;
    }
    
    public static function getLeaderboardEntries() {
        $resultset = Leaderboards::getEntriesBaseResultset();
        
        $resultset->addFilterCriteria('l.is_daily_ranking = 1');
        $resultset->setSortCriteria('l.daily_date', 'ASC');
        $resultset->addSortCriteria('le.rank', 'ASC');
        
        return $resultset;
    }
}