<?php

namespace Modules\Necrolab\Models\Dailies\Seasons\Cache;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;
use \Modules\Necrolab\Models\Leaderboards\Cache\CacheNames as LeaderboardCacheNames;

class CacheNames
extends BaseCacheNames {    
    const SEASON = 's';
    
    const LATEST_SEASON = 'lt';
    
    const NEXT_SEASON = 'nt';
    
    const LEADERBOARDS_BY_DATE = 'dt';
    
    const LEADERBOARD_SCORE_TOTALS = 'st';
    
    const RANKINGS = 'ds';
    
    const RANKING_TOTAL_POINTS = 'tp';
    
    const RANKING_ENROLLMENT = 'en';
    
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
    
    /* ---------- Rankings ---------- */
    
    public static function getSeasonsName() {
        return self::RANKINGS . ':' . self::SEASON;
    }
    
    public static function getLatestSeasonName() {
        return self::RANKINGS . ':' . self::LATEST_SEASON;
    }
    
    public static function getNextSeasonName() {
        return self::RANKINGS . ':' . self::NEXT_SEASON;
    }
    
    public static function getRankingName($season_number) {
        return self::RANKINGS .  ':' . self::SEASON . "{$season_number}";
    }
    
    public static function getRankingEntriesName($season_number) {
        return self::getRankingName($season_number) . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getRankingEntryName($season_number, $steamid) {
        return self::getRankingEntriesName($season_number) . ":{$steamid}";
    }
    
    public static function getRankingEntriesFilterName($season_number) {
        return self::getRankingEntriesName($season_number) . ':' . BaseCacheNames::FILTER;
    }
    
    public static function getRankingTotalPointsName($season_number) {
        return self::getRankingName($season_number) . ':' . self::RANKING_TOTAL_POINTS;
    }
    
    public static function getRankingEnrollmentName($season_number) {
        return self::getRankingName($season_number) . ':' . self::RANKING_ENROLLMENT;
    }
    
    public static function getRankingEnrollmentEntriesName($season_number) {
        return self::getRankingEnrollmentName($season_number) . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getRankingEnrollmentEntryName($season_number, $steamid) {
        return self::getRankingEnrollmentEntriesName($season_number) . ":{$steamid}";
    }
    
    public static function getRankingLeaderboardByDateName($season_number) {
        return self::getRankingName($season_number) . ':' . self::LEADERBOARDS_BY_DATE;
    }
}