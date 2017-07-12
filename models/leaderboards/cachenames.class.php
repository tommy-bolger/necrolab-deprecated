<?php

namespace Modules\Necrolab\Models\Leaderboards;

use \Modules\Necrolab\Models\CacheNames as BaseCacheNames;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames as RankingCacheNames;

class CacheNames
extends BaseCacheNames {
    const LEADERBOARDS = 'l';
    
    const RECORDS = 'r';
    
    const REPLAYS = 'sr';
    
    const SNAPSHOTS = 'ss';
    
    const DAILIES = 'd';
    
    /* ---------- Leaderboards ---------- */ 
    
    public static function getLeaderboardsName() {
        return self::LEADERBOARDS;
    }
    
    public static function getRecordsName() {
        return self::getLeaderboardsName() . ':' . self::RECORDS;
    }
    
    public static function getRecordsIndexName(array $index_segments) {                
        return parent::getIndexName(self::getLeaderboardsName() . ':' . BaseCacheNames::INDEX, $index_segments);
    }
    
    public static function getIdsName() {
        return self::getLeaderboardsName() . ':'  . BaseCacheNames::IDS;
    }
    
    public static function getEntriesName($leaderboard_id) {
        return self::getLeaderboardsName() . ':' . BaseCacheNames::ENTRIES . ":{$leaderboard_id}";
    }
    
    public static function getIndexName($leaderboard_id, array $index_segments) {                
        return parent::getIndexName(self::getEntriesName($leaderboard_id) . ':' . BaseCacheNames::INDEX, $index_segments);
    }    
    
    public static function getDailyLeaderboardsName() {
        return self::LEADERBOARDS . ':' . self::DAILIES;
    }
    
    public static function getDailyEntriesName() {
        return self::getDailyLeaderboardsName() . ':' . BaseCacheNames::ENTRIES;
    }
    
    public static function getDailyIndexName(array $index_segments) {                
        return parent::getIndexName(self::getDailyEntriesName() . ':' . BaseCacheNames::INDEX, $index_segments);
    }    
    
    public static function getSnapshotsName() {
        return self::getLeaderboardsName() . ':' . self::SNAPSHOTS;
    }
    
    public static function getAllSnapshotsName() {
        return self::getSnapshotsName();
    }
    
    public static function getSnapshotsIndexName($leaderboard_id) {
        return parent::getIndexName(self::getSnapshotsName() . ':' . BaseCacheNames::INDEX, array(
            $leaderboard_id
        ));
    }
    
    public static function getReplaysName() {
        return self::REPLAYS;
    }
    
    public static function getAllReplaysName() {
        return self::getReplaysName() . ':' . self::RECORDS;
    }
    
    public static function getReplaysIndexName(array $index_segments = array()) {
        return parent::getIndexName(self::getReplaysName() . ':' . BaseCacheNames::INDEX, $index_segments);
    }
}