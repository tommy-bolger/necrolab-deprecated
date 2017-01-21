<?php
namespace Modules\Necrolab\Models\Dailies\Seasons\Leaderboards\Cache;

use \PDO;
use \DateTime;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\Dailies\Seasons\Entry as BaseEntry;
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

class Entry
extends BaseEntry {                   
    public static function save(array $leaderboard_entry, $leaderboard_record, $season_number, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        Leaderboards::saveEntry($leaderboard_entry, $leaderboard_record, CacheNames::getRankingName($season_number), $cache);
        
        $cache->hSet(SteamUserCacheNames::getDailySeasonByDateLeaderboardsName($season_number, $leaderboard_entry['steamid']), $leaderboard_record->daily_date, $leaderboard_entry['rank']);
    }
    
    public static function loadLeaderboardEntriesIntoCache($cache = NULL) {    
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $leaderboards_resultset = DatabaseDailySeasons::getSeasonLeaderboards();
        
        $leaderboards = $leaderboards_resultset->getAssoc();
        
        $transaction = $cache->transaction();
        
        $daily_entries_resultset = DatabaseDailySeasons::getSeasonLeaderboardEntries();

        $max_ranks = array();
        $daily_entries = $daily_entries_resultset->prepareExecuteQuery();

        while($daily_entry = $daily_entries->fetch(PDO::FETCH_ASSOC)) {            
            $season_number = $daily_entry['season_number'];
            $lbid = $daily_entry['lbid'];
            
            $leaderboard_record = new Leaderboard();
            $leaderboard_record->setPropertiesFromArray($leaderboards[$lbid][0]);
            $leaderboard_record->lbid = $lbid;
        
            static::saveLeaderboardEntry($daily_entry, $leaderboard_record, $season_number, $transaction);
            
            $max_ranks[$season_number][$lbid] = $daily_entry['rank'];
        }
        
        if(!empty($max_ranks)) {
            foreach($max_ranks as $season_number => $leaderboards) {
                foreach($leaderboards as $lbid => $max_rank) {
                    static::saveLeaderboardEntries($season_number, $lbid, $max_rank, $transaction);
                }
            }
        }

        $transaction->commit();
    }
}