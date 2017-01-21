<?php

namespace Modules\Necrolab\Models\Dailies\Leaderboards\Cache;

use \Modules\Necrolab\Models\Dailies\Rankings\Cache\CacheNames as BaseCacheNames;
use \Modules\Necrolab\Models\Leaderboards\Cache\CacheNames as LeaderboardCacheNames;

class CacheNames
extends BaseCacheNames {            
    const LEADERBOARDS_BY_DATE = 'd';
    
    const LEADERBOARDS_SCORE_TOTALS = 'st';
    
    public static function getLeaderboardsName() {
        return self::getRankingsBaseName() . ':' . LeaderboardCacheNames::getLeaderboardsName();
    }
    
    public static function getLeaderboardName($lbid) {
        return self::getRankingsBaseName() . ':' . LeaderboardCacheNames::getLeaderboardName($lbid);
    }
    
    public static function getLeaderboardEntriesName($lbid) {
        return self::getRankingsBaseName() . ':' . LeaderboardCacheNames::getEntriesName($lbid);
    }
    
    public static function getLeaderboardEntryName($lbid, $entry_number) {
        return self::getRankingsBaseName() . ':' . LeaderboardCacheNames::getEntryName($lbid, $entry_number);
    }
    
    public static function getLeaderboardsByDateName() {
        return self::getLeaderboardsName() . ':' . self::LEADERBOARDS_BY_DATE;
    }
    
    public static function getLeaderboardScoreTotalsName() {
        return self::getLeaderboardsName() . ':' . self::LEADERBOARDS_SCORE_TOTALS;
    }
}