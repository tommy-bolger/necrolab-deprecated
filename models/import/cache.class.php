<?php
namespace Modules\Necrolab\Models\Import;

use \Exception;
use \DateTime;
use \DateInterval;
use \Modules\Necrolab\Models\Leaderboards\Cache\Leaderboards;
use \Modules\Necrolab\Models\Leaderboards\Cache\DailyRankings;
use \Modules\Necrolab\Models\DailyRankings;
use \Modules\Necrolab\Models\SteamUsers;

class Cache
extends Import {                
    protected function loadLatestDailySeason() {
        $latest_daily_season_number = $this->cache_read->get(CacheEntryNames::DAILY_SEASON_LATEST_NUMBER);
    
        if(!empty($latest_daily_season_number)) {
            $this->latest_daily_season = $this->cache_read->hGetAll(CacheEntryNames::generateDailyRankingSeasonName($latest_daily_season_number));
        }
    }
    
    protected function loadLatestDailySeasonUsers() {
        if(!empty($this->latest_daily_season)) {
            $this->latest_daily_season_users = $this->cache_read->hGetAll(CacheEntryNames::generateDailyRankingSeasonEnrollmentName($this->latest_daily_season['season_number']));
        }
    }
    
    protected function saveImportedLeaderboards() {
        
    }
    
    protected function saveNewUsers() {    
        if(!empty(self::$new_steam_users)) {        
            $user_transaction = $this->cache->transaction();
        
            foreach(self::$new_steam_users as $steamid) {                                            
                SteamUsers::saveUserToCache(array(
                    'steamid' => $steamid,
                    'updated' => strtotime('-30 days')
                ), $user_transaction, array(
                    'steamid' => $steamid
                ));
            }
            
            $user_transaction->commit();
        }
    }
}