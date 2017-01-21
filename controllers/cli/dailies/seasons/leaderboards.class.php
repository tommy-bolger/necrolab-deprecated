<?php
namespace Modules\Necrolab\Controllers\Cli\Dailies\Seasons;

use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Leaderboards\Cache\RecordModels\Leaderboard;
use \Modules\Necrolab\Models\Leaderboards\Cache\RecordModels\ScoreEntry;
use \Modules\Necrolab\Models\SteamUsers\Cache\CacheNames as SteamUserCacheNames;
use \Modules\Necrolab\Models\DailyRankings\Cache\CacheNames as DailyRankingsCacheNames;

class Leaderboards
extends Cli {
    protected $cache;

    protected $season_number;
    
    protected $enrollment_entries = array();

    protected $lbid;
    
    protected $leaderboard_record;
    
    protected $leaderboard_rank_index = array();
    
    public function init() {
        $this->cache = cache('write');
    }
    
    public function __construct($season_number, array $enrollment_entries, $lbid) {        
        $this->season_number = $season_number;
        $this->enrollment_entries = $enrollment_entries;
        $this->lbid = $lbid;
        
        $leaderboard = $this->cache->lRange(DailyRankingsCacheNames::getLeaderboardName($this->lbid));
        
        $this->leaderboard_record = new Loaderboard();
        $this->leaderboard_record->setPropertiesFromIndexedArray($leaderboard);
    }

    public function saveEntriesChunk($daily_entries, $leaderboard_ranks) {
        if(!empty($daily_entries)) {
            $transaction = cache('write')->transaction();
        
            foreach($daily_entries as $daily_entry) {
                if(!empty($daily_entry)) {
                    $daily_leaderboard_entry = new ScoreEntry();
                        
                    $daily_leaderboard_entry->setPropertiesFromIndexedArray($daily_entry);
                    
                    $new_rank = $leaderboard_ranks[$daily_leaderboard_entry->rank] + 1;
                    
                    $daily_leaderboard_entry->rank = $new_rank;
                    
                    DailySeasons::saveLeaderboardEntry($daily_leaderboard_entry->toArray(), $this->leaderboard_record, $this->season_number, $transaction);
                }
            }
            
            $transaction->commit();
        }
    }
    
    public function saveEntries() {        
        if(!empty($this->leaderboard_rank_index)) {            
            sort($this->leaderboard_rank_index);
        
            $leaderboard_ranks = array_flip($this->leaderboard_rank_index);
        
            $transaction = $this->cache->transaction();
            
            $transaction->setCommitProcessCallback(array($this, 'saveEntriesChunk'), array(
                'leaderboard_ranks' => $leaderboard_ranks
            ));
        
            foreach($leaderboard_ranks as $daily_leaderboard_rank => $rank) {
                $daily_entry_name = DailyRankingsCacheNames::getLeaderboardEntryName($this->lbid, $daily_leaderboard_rank);
            
                $transaction->lRange($daily_entry_name, 0, -1);
            }
            
            $transaction->commit();
            
            $transaction = $this->cache->transaction();
            
            $max_rank = end($leaderboard_ranks) + 1;
            
            DailySeasons::saveLeaderboardEntries($this->season_number, $this->lbid, $max_rank, $transaction);
            
            DailySeasons::saveLeaderboard($this->leaderboard_record->toArray(), $transaction);
            
            $transaction->commit();
        }
    }
    
    public function processRanksChunk($leaderboard_ranks) {
        if(!empty($leaderboard_ranks)) {        
            foreach($leaderboard_ranks as $leaderboard_rank) {
                if(!empty($leaderboard_rank)) {
                    $this->leaderboard_rank_index[] = $leaderboard_rank;
                }
            }
        }
    }
    
    public function actionGenerateForDate(DateTime $date) {
        if(!empty($this->enrollment_entries)) {
            $this->cache = cache('write');
            
            $transaction = $this->cache->transaction();
            
            $transaction->setCommitProcessCallback(array($this, 'processRanksChunk'));
        
            foreach($this->enrollment_entries as $enrollment_entry) {
                $steamid = $enrollment_entry['steamid'];
                
                $user_leaderboards_name = SteamUserCacheNames::getDailyLeaderboardsName($this->lbid);
                
                $transaction->hGet($user_leaderboards_name, $this->lbid);
            }
            
            $transaction->commit();
            
            $this->saveEntries();
        }
    }
    
    /*public static function processDailyLeaderboardsChunk($leaderboards, $season_number, $enrollment_entries) {
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
        cache('write')->reval(static::getLuaScriptPath() . '/generate_season_leaderboards.lua', array(
            CacheNames::generateSeasonName($season_number),
            BaseCacheNames::ENTRIES,
            CacheNames::generateDailyRankingSeasonEnrollmentName($season_number),
            CacheNames::generateSeasonLeaderboardScoreTotalsName($season_number),
            CacheNames::TEMP_NAME
        ));
        
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
        
    }*/
}