<?php
namespace Modules\Necrolab\Models\Dailies\Seasons\Leaderboards\Cache;

use \PDO;
use \DateTime;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\Dailies\Seasons\Leaderboards as BaseLeaderboards;
use \Modules\Necrolab\Models\DailySeasons\Database\DailySeasons as DatabaseDailySeasons;
use \Modules\Necrolab\Models\DailySeasons\Cache\RecordModels\DailySeason;
use \Modules\Necrolab\Models\DailySeasons\Cache\RecordModels\DailySeasonEntry;
use \Modules\Necrolab\Models\DailySeasons\Cache\RecordModels\DailySeasonEnrollment;
use \Modules\Necrolab\Models\Leaderboards\Cache\Leaderboards;
use \Modules\Necrolab\Models\Leaderboards\Cache\RecordModels\Leaderboard;
use \Modules\Necrolab\Models\Leaderboards\Cache\RecordModels\LeaderboardEntry;
use \Modules\Necrolab\Models\SteamUsers\Cache\CacheNames as SteamUserCacheNames;
use \Modules\Necrolab\Models\DailyRankings\Cache\DailyRankings;
use \Modules\Necrolab\Models\DailyRankings\Cache\CacheNames as DailyRankingsCacheNames;

class Leaderboards
extends BaseLeaderboards {                   
    public static function save(array $leaderboard_record, $cache = NULL) {
        if(!empty($leaderboard_record)) {
            if(empty($cache)) {
                $cache = cache('write');
            }
        
            $season_number = $leaderboard_record['season_number'];
            $lbid = $leaderboard_record['lbid'];
            
            Leaderboards::save($leaderboard_record, $lbid, CacheNames::getRankingName($season_number), $cache);
        
            $leaderboard_name = CacheNames::getLeaderboardName($season_number, $lbid);

            $cache->hSetNx(CacheNames::getLeaderboardsByDateName($season_number), $leaderboard_record['daily_date'], $leaderboard_name);
        }
    }
    
    public static function saveLeaderboardEntries($season_number, $lbid, $max_rank, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        Leaderboards::saveEntries($lbid, $max_rank, CacheNames::getRankingName($season_number), $cache);
    }
    
    public static function saveLeaderboardEntry(array $leaderboard_entry, $leaderboard_record, $season_number, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        Leaderboards::saveEntry($leaderboard_entry, $leaderboard_record, CacheNames::getRankingName($season_number), $cache);
        
        $cache->hSet(SteamUserCacheNames::getDailySeasonByDateLeaderboardsName($season_number, $leaderboard_entry['steamid']), $leaderboard_record->daily_date, $leaderboard_entry['rank']);
    }
    
    public static function loadLeaderboardsIntoCache($cache = NULL) {    
        if(empty($cache)) {
            $cache = cache('write');
        }

        $transaction = $cache->transaction();
        
        $daily_leaderboards_resultset = DatabaseDailySeasons::getSeasonLeaderboards();
            
        $daily_leaderboards = $daily_leaderboards_resultset->prepareExecuteQuery();                

        while($daily_leaderboard = $daily_leaderboards->fetch(PDO::FETCH_ASSOC)) {
            static::saveLeaderboard($daily_leaderboard, $transaction);
        }
            
        $transaction->commit();
    }
}