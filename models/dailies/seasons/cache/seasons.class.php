<?php
namespace Modules\Necrolab\Models\Dailies\Seasons\Cache;

use \PDO;
use \DateTime;
use \Framework\Data\ResultSet\Redis\HashStructure as Redis;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\Dailies\Seasons\Seasons as BaseSeasons;
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

class Seasons
extends BaseSeasons {
    protected static function loadAll() {
        if(empty(static::$seasons)) {
            $season_entries = cache('read')->sMembers(CacheNames::getSeasonsName());

            if(!empty($season_entries)) {
                static::$seasons = cache('read')->hGetAllMulti($season_entries);
            }
        }
    }
    
    public static function getRankingsResultset($season_number) {
        $resultset = new Redis(CacheNames::getRankingName($season_number), cache('read'));
        
        $resultset->setEntriesName(CacheNames::getRankingEntriesName($season_number));
        $resultset->setFilterName(CacheNames::getRankingEntriesFilterName($season_number));
        
        $resultset->setRowsPerPage(100);
        
        return $resultset;
    }
    
    public static function save(array $season, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        } 
    
        $daily_season = new DailySeason();
        $daily_season->setPropertiesFromArray($season);
        
        $season_hash_name = CacheNames::getRankingName($daily_season->season_number);
        $record_fields = $daily_season->toArray(false);
        
        $cache->hMSet($season_hash_name, $record_fields);
                
        $cache->sAdd(CacheNames::getSeasonsName(), $season_hash_name);
        
        if($daily_season->is_latest == 1) {
            $cache->set(CacheNames::getLatestSeasonName(), $daily_season->season_number);
        }
    }
    
    public static function saveEnrollmentEntry($season_number, array $enrollment_record, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }  
        
        $enrollment_entry = new DailySeasonEnrollment();
        $enrollment_entry->setPropertiesFromArray($enrollment_record);
        
        $enrollment_entry_name = CacheNames::getRankingEnrollmentEntryName($season_number, $enrollment_entry->steamid);
        
        $record_fields = $enrollment_entry->toArray(false);
        
        $cache->hMSet($enrollment_entry_name, $record_fields);
        
        $cache->sAdd(CacheNames::getRankingEnrollmentEntriesName($season_number), $enrollment_entry_name);
    }
    
    public static function saveSeasonEntry($season_number, array $entry_data, $cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }    
        
        $season_entry = new DailySeasonEntry();
        $season_entry->setPropertiesFromArray($entry_data);
        
        $record_fields = $season_entry->toArray(false);
        
        if(!empty($record_fields)) {
            $entry_hash_name = CacheNames::getRankingEntryName($season_number, $season_entry->rank);
        
            foreach($record_fields as $record_field) {
                $cache->rPush($entry_hash_name, $record_field);
            }
        
            $cache->set(CacheNames::getRankingEntriesName($season_number), $season_entry->rank);
        }
    }
    
    public static function saveLeaderboard(array $leaderboard_record, $cache = NULL) {
        if(!empty($leaderboard_record)) {
            if(empty($cache)) {
                $cache = cache('write');
            }
        
            $season_number = $leaderboard_record['season_number'];
            $lbid = $leaderboard_record['lbid'];
            
            Leaderboards::saveLeaderboard($leaderboard_record, $lbid, CacheNames::getRankingName($season_number), $cache);
        
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
    
    public static function loadSeasonsInCache($cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $seasons = DatabaseDailySeasons::getSeasons();
        
        if(!empty($seasons)) {
            $transaction = $cache->transaction();
        
            $latest_season_name = NULL;
            $next_season_name = NULL;
        
            foreach($seasons as $season) {
                $season_number = $season['season_number'];
                $season_ranking_name = CacheNames::getRankingName($season_number);
            
                if($season['is_latest'] == 1) {
                    $latest_season_name = $season_ranking_name;
                }
                
                if(!empty($latest_season_name) && empty($next_season_name)) {
                    $next_season_name = $season_ranking_name;
                }
                
                static::saveSeason($season, $transaction); 
            }
            
            if(!empty($next_season_name)) {
                $transaction->set(CacheNames::getNextSeasonName(), $next_season_name);
            }
            
            $transaction->commit();
        }
    }
    
    public static function loadEnrollmentInCache() {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $transaction = $cache->transaction();
        
        $season_enrollment_resultset = DatabaseDailySeasons::getSeasonEnrollment();
                
        $season_enrollment = $season_enrollment_resultset->getAll();
        
        if(!empty($season_enrollment)) {        
            foreach($season_enrollment as $enrollment_record) {
                static::saveEnrollmentEntry($enrollment_record['season_number'], $enrollment_record, $transaction);
            }
        } 
    
        $transaction->commit();
    }
    
    public static function loadSeasonEntriesInCache($cache = NULL) {
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $transaction = $cache->transaction();
    
        $latest_entries_resultset = DatabaseDailySeasons::getSeasonEntriesResultset();
            
        $daily_season_rankings = $latest_entries_resultset->prepareExecuteQuery();

        while($daily_season_ranking = $daily_season_rankings->fetch(PDO::FETCH_ASSOC)) {
            static::saveSeasonEntry($season_number, $daily_season_ranking, $transaction);
            
            $season_enrollment_resultset = DatabaseDailySeasons::getSeasonEnrollment($season['daily_ranking_season_id']);
                
            $season_enrollment = $season_enrollment_resultset->getAll();
            
            if(!empty($season_enrollment)) {
                $enrollment_entries_name = CacheNames::getRankingEnrollmentEntriesName($season_number);
            
                foreach($season_enrollment as $enrollment_record) {
                    $steamid = $enrollment_record['steamid'];
                
                    $enrollment_entry_name = CacheNames::getRankingEnrollmentEntryName($season_number, $steamid);
                    
                    $enrollment_entry = new DailySeasonEnrollment();
                    $enrollment_entry->setPropertiesFromArray($enrollment_record);
                
                    $transaction->hMSet($enrollment_entry_name, $enrollment_entry->toArray(false));
                    $transaction->hSet($enrollment_entries_name, $steamid, $enrollment_entry_name);
                }
            }        
        }
        
        $transaction->commit();
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
    
    
    
    public static function processDailyLeaderboardsChunk($leaderboards, $season_number, $enrollment_entries) {
        if(!empty($leaderboards)) {
            $transaction = cache('write')->transaction();
            
            $leaderboard_records = array();
                    
            foreach($leaderboards as $leaderboard) {
                if(!empty($leaderboard)) {
                    $leaderboard_record = new Leaderboard();
                    $leaderboard_record->setPropertiesFromIndexedArray($leaderboard);
                    
                    $lbid = $leaderboard_record->lbid;
                    
                    $leaderboard_entry_name = CacheNames::getLeaderboardName($season_number, $lbid);
                    
                    $transaction->hMSet($leaderboard_entry_name, $lbid);
                    $transaction->hSet(CacheNames::getLeaderboardsName($season_number), $lbid, $leaderboard_entry_name);
                    $transaction->hSet(CacheNames::getLeaderboardsByDateName($season_number), $leaderboard_record->daily_date, $leaderboard_entry_name);
                    
                    $leaderboard_records[] = $leaderboard_record;
                }
            }
            
            $transaction->commit();
        
        
            if(!empty($leaderboard_records)) {
                foreach($enrollment_entries as $enrollment_entry) {
                    $unenrolled = NULL;
                            
                    if(!empty($enrollment_entry['unenrolled'])) {
                        $unenrolled = new DateTime($enrollment_entry['unenrolled']);
                    }
                
                    foreach($leaderboard_records as $leaderboard_record) {                                   
                        
                        
                        $lbid = $leaderboard_record->lbid;
                        $daily_date = new DateTime($leaderboard_record->daily_date);
                    
                        foreach($enrollment_entries as $enrollment_entry) {
                            
                        }
                    
                        $transaction = cache('write')->transaction();
                        
                        $transaction->setCommitProcessCallback(array(get_called_class(), 'processLeaderboardEntriesChunk'), array(
                            'leaderboard_record' => $leaderboard_record,
                            'number_of_days' => $number_of_days
                        ));
                        
                        for($entry_number = 1; $entry_number <= $number_of_entries; $entry_number++) {
                            $transaction->lRange(CacheNames::getLeaderboardEntryName($lbid, $entry_number), 0, -1);
                        }
                        
                        $transaction->commit();
                    }
                }
            }
        }
    }
    
    public static function generateLeaderboards($season_number) {
        /*cache('write')->reval(static::getLuaScriptPath() . '/generate_season_leaderboards.lua', array(
            CacheNames::generateSeasonName($season_number),
            BaseCacheNames::ENTRIES,
            CacheNames::generateDailyRankingSeasonEnrollmentName($season_number),
            CacheNames::generateSeasonLeaderboardScoreTotalsName($season_number),
            CacheNames::TEMP_NAME
        ));*/
        
        $cache = cache('write');
        
        $season_record = $cache->hGetAll(CacheNames::getRankingName($season_number));
        
        if(!empty($season_record)) {
            $enrollment_entry_names = $cache->sMembers(CacheNames::getRankingEnrollmentEntriesName($season_number));
            
            if(!empty($enrollment_entry_names)) {
                $season = new DailySeason();
                $season->setPropertiesFromArray($season_record);
                
                $season_start_date = new DateTime($season_record->start_date);
                $season_end_date = new DateTime($season_record->end_date);
                
                $enrollment_entries = $cache->hGetAllMulti($enrollment_entry_names);
            
                $daily_leaderboards = $cache->hGetAll(DailyRankingsCacheNames::getRankingsName());
                $daily_leaderboards_in_season = array();
                
                if(!empty($daily_leaderboards)) {            
                    foreach($daily_leaderboards as $date => $leaderboard_name) {
                        if(static::dateInRange(new DateTime($date), $season_start_date, $season_end_date)) {
                            $daily_leaderboards_in_season[] = $leaderboard_name;
                        }
                    }
                }
                
                if(!empty($daily_leaderboards_in_season)) {
                    $transaction = $cache->transaction();
            
                    $transaction->setCommitProcessCallback(array(get_called_class(), 'processLeaderboardsChunk'), array(
                        'season_number' => $season_number,
                        'enrollment_entries' => $enrollment_entries
                    ));
                
                    foreach($daily_leaderboards_in_season as $daily_leaderboard_name) {
                        $transaction->lRange($daily_leaderboard_name, 0, -1);
                    }
                    
                    $transaction->commit();
                }
            }
        }
        
        /*
        local season_leaderboard_enrollment = collate(redis.call('HGETALL', season_leaderboard_entrollment_name))

do
    local season_leaderboards = collate(redis.call('HGETALL', season_leaderboard_entries_name))
    
    for lbid, leaderboard_hash_name in pairs(season_leaderboards) do
        local leaderboard_entries_temp_name = temp_name .. ':' .. season_leaderboards_name .. ':' .. lbid .. ':' .. entry_name
        
        for steamid, steam_user_hash_name in pairs(season_leaderboard_enrollment)
            local leaderboard_entry_hash_name = leaderboard_hash_name .. ':' .. entry_name .. ':' .. steamid
            
            local leaderboard_entry_exists = redis.call('EXISTS', leaderboard_entry_hash_name)
            
            if leaderboard_entry_exists == 1 then
                -- Retrieve the daily leaderboard and add it as a season leaderboard
                local leaderboard_entry = collate(redis.call('HGETALL', leaderboard_entry_hash_name))
                
                local score = leaderboard_entry['score'];
                
                local season_leaderboard_entry_hash_name = season_leaderboards_name .. ':' .. lbid .. ':' .. entry_name .. steamid
                
                hmset(season_leaderboard_entry_hash_name, leaderboard_entry)
                
                redis.call('ZADD', leaderboard_entries_temp_name, score, season_leaderboard_entry_hash_name)
                
                redis.call('HINCRBY', season_score_totals_temp_name, season_leaderboard_entry_hash_name, score);
            end
        end        
        
        local leaderboard_entry_scores = redis.call('ZREVRANGE', leaderboard_entries_temp_name, 0, -1)
        
        for rank, leaderboard_entry_name in ipairs(leaderboard_entry_scores) do
            redis.call('HSET', leaderboard_entry_name, 'rank', rank)
            
            redis.call('ZADD', leaderboard_entries_temp_name, rank, leaderboard_entry_name)
        end
        
        -- Rename the master lists to override any existing permanent key
        local leaderboard_entries_name = season_leaderboards_name .. ':' .. lbid .. ':' .. entry_name
        
        redis.call('RENAME', leaderboard_entries_temp_name, leaderboard_entries_name)
        
        redis.call('RENAME', season_score_totals_temp_name, season_score_totals_name)
    end
end
        */
    }
    
    public static function generateRankings($season_number) {
        cache('write')->reval($daily_ranking_generation_lua_script, array(
            CacheNames::getLeaderboardsName($season_number),
            BaseCacheNames::ENTRIES,
            CacheNames::getRankingName($season_number),
            CacheNames::getRankingEntriesName($season_number),
            CacheNames::getRankingTotalPointsName($season_number),
            CacheNames::TEMP_NAME,
            CacheNames::STEAM_USERS
        ));
    }
}