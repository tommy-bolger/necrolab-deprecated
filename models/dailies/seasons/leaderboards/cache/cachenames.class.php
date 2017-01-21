<?php

namespace Modules\Necrolab\Models\Dailies\Seasons\Leaderboards\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;
use \Modules\Necrolab\Models\Leaderboards\Cache\CacheNames as LeaderboardCacheNames;

class CacheNames
extends BaseCacheNames {    
    const SEASON = 's';
    
    const LEADERBOARDS_BY_DATE = 'dt';
    
    const LEADERBOARD_SCORE_TOTALS = 'st';
    
    const RANKINGS = 'ds';
    
    /* ---------- Leaderboards ---------- */
    
    public static function getLeaderboardSeasonName($season_number) {
        return self::RANKINGS . ':' . self::SEASON . $season_number . ':' . LeaderboardCacheNames::LEADERBOARDS;
    }
    
    public static function getLeaderboardsName($season_number) {
        return self::getLeaderboardSeasonName($season_number);
    }
    
    public static function getLeaderboardName($season_number, $lbid) {
        return self::getLeaderboardSeasonName($season_number) . ":{$lbid}";
    }
    
    public static function getLeaderboardEntriesName($season_number, $lbid) {
        return self::getLeaderboardName($season_number, $lbid) . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getLeaderboardEntryName($season_number, $lbid, $steamid) {
        return self::getLeaderboardEntriesName($season_number, $lbid) . ":{$steamid}";
    }
    
    public static function getLeaderboardEntriesFilterName($season_number, $lbid) {
        return self::getLeaderboardEntriesName($season_number, $lbid) . ':' . BaseCacheNames::FILTER;
    }
    
    public static function getLeaderboardsByDateName($season_number) {
        return self::getLeaderboardsName($season_number) . ':' . self::LEADERBOARDS_BY_DATE;
    }
    
    public static function getLeaderboardScoreTotalsName($season_number) {
        return self::getLeaderboardsName($season_number) . ':' . self::LEADERBOARD_SCORE_TOTALS;
    }
}