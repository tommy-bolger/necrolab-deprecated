<?php
namespace Modules\Necrolab\Models\Import;

use \Exception;
use \DateTime;
use \Framework\Data\Database as FrameworkDatabase;
use \Modules\Necrolab\Models\DailyRankings;
use \Modules\Necrolab\Models\DailyRankingSeasons;
use \Modules\Necrolab\Models\DailyRankingSeasonLeaderboards;
use \Modules\Necrolab\Objects\CacheEntryNames;

class Database
extends Import {
    protected $steam_user_ids = array();
    
    public function __construct($cli_framework, $verbose_output) {   
        parent::__construct($cli_framework, $verbose_output);
        
        $this->cache = cache("database_temp", true);
    }
    
    protected function loadCharacterCache() {    
        if($this->verbose_output) {
            $this->framework->coutLine("Loading character cache.");
        }
        
        $this->characters = db()->getMappedColumn("
            SELECT
                name,
                character_id
            FROM characters
        ");
    }
    
    protected function loadBlacklistCache() {    
        if($this->verbose_output) {
            $this->framework->coutLine("Loading blacklist cache.");
        }
        
        $this->leaderboards_blacklist = db()->getMappedColumn("
            SELECT 
                l.lbid AS lbid_1,
                l.lbid AS lbid_2
            FROM leaderboards_blacklist lb
            JOIN leaderboards l ON l.leaderboard_id = lb.leaderboard_id
        ");
    }
    
    protected function loadDailyRankingDayTypes() {
        $this->daily_ranking_day_types = db()->getGroupedRows("
            SELECT *
            FROM daily_ranking_day_types
            WHERE enabled = 1
        ");
    }
    
    protected function loadLatestDailySeason() {
        $this->latest_daily_season = DailyRankingSeasons::getLatestSeason();
    }
    
    protected function loadLatestDailySeasonUsers() {
        if(!empty($this->latest_daily_season)) {
            $this->latest_daily_season_users = db()->getMappedColumn("
                SELECT 
                    su.steamid,
                    drse.steam_user_id
                FROM daily_ranking_season_enrollment drse
                JOIN steam_users su ON su.steam_user_id = drse.steam_user_id
                WHERE drse.daily_ranking_season_id = ?
            ", array(
                $this->latest_daily_season['daily_ranking_season_id']
            ));
        }
    }
    
    protected function loadUsersCache() {
        self::$users = db()->getMappedColumn("
            SELECT 
                steamid,
                steam_user_id
            FROM steam_users
        ");
    }
    
    protected function saveImportedLeaderboards() {  
        if(!empty($this->imported_leaderboards)) {
            if($this->verbose_output) {
                $this->framework->coutLine("===== Saving imported leaderboards to the database. =====");
            }
        
            $stored_leaderboards = db()->getMappedColumn("
                SELECT
                    lbid,
                    leaderboard_id
                FROM leaderboards
            ");
            
            $local_transaction = $this->cache->transaction();
        
            foreach($this->imported_leaderboards as $lbid => $imported_leaderboard) {
                $lbid = $imported_leaderboard['lbid'];
                $leaderboard_id = NULL;
                
                $imported_leaderboard_cache_record = $imported_leaderboard;
                
                //Remove data that isn't needed to save memory
                unset($imported_leaderboard_cache_record['url']);
                unset($imported_leaderboard_cache_record['displaytype']);
                unset($imported_leaderboard_cache_record['onlyfriendsreads']);
                unset($imported_leaderboard_cache_record['sortmethod']);
                unset($imported_leaderboard_cache_record['character_id']);
                unset($imported_leaderboard_cache_record['onlytrustedwrites']);
                unset($imported_leaderboard_cache_record['entries']);
                unset($imported_leaderboard_cache_record['display_name']);             
                
                //Remove null daily_date values. This is to to make lua script properly convert hashes into tables. Any null values cause the whole table to be nil in lua. 
                if(empty($imported_leaderboard_cache_record['daily_date'])) {
                    unset($imported_leaderboard_cache_record['daily_date']);
                }
                
                $hash_name = CacheEntryNames::generateLeaderboardName($lbid);
            
                $local_transaction->hMSet($hash_name, $imported_leaderboard_cache_record);                
                $local_transaction->hSet(CacheEntryNames::LEADERBOARDS, $lbid, $hash_name);   
                
                if(!empty($imported_leaderboard['is_power_ranking'])) {
                    $local_transaction->hSet(CacheEntryNames::LEADERBOARDS_POWER, $lbid, $hash_name);
                }
                
                unset($imported_leaderboard['character_name']);
            
                if(empty($stored_leaderboards[$lbid])) {                
                    if($this->verbose_output) {
                        $this->framework->coutLine("Saving leaderboard '{$imported_leaderboard['name']}' to the database.");
                    }
                    
                    $leaderboard_id = db()->insert('leaderboards', $imported_leaderboard, 'add_leaderboard');
                }
                else {                       
                    $leaderboard_id = $stored_leaderboards[$lbid];
                    
                    db()->update('leaderboards', $imported_leaderboard, array(
                        'leaderboard_id' => $leaderboard_id
                    ));
                }
            
                $this->imported_leaderboards[$lbid]['leaderboard_id'] = $leaderboard_id;
            }
            
            $local_transaction->commit();
        }
    }
    
    protected function importLeaderboardEntries() {
        FrameworkDatabase::destroyInstance();
        
        parent::importLeaderboardEntries();
    }
    
    public function processLeaderboardEntriesChunk($leaderboard_entries, $lbid, $leaderboard_snapshot_id) {        
        if(!empty($leaderboard_entries)) {                
            $leaderboard_record = $this->imported_leaderboards[$lbid];
            
            $placeholder_record = array(
                'leaderboard_id' => NULL,
                'leaderboard_snapshot_id' => NULL,
                'steam_user_id' => NULL,
                'score' => 0,
                'rank' => NULL,
                'ugcid' => NULL,
                'details' => NULL,
                'time' => NULL,
                'is_win' => 0
            );
        
            foreach($leaderboard_entries as $leaderboard_entry) {              
                $steamid = $leaderboard_entry['steamid'];
                unset($leaderboard_entry['steamid']);
                
                $steam_user_id = NULL;
                
                if(!empty(self::$users[$steamid])) {
                    $steam_user_id = self::$users[$steamid];
                }
                else {                
                    $steam_user_id = db()->insert('steam_users', array(
                        'steamid' => $steamid
                    ));
                    
                    self::$users[$steamid] = $steam_user_id;
                }
                
                $lbid = $leaderboard_entry['lbid'];
                unset($leaderboard_entry['lbid']);
        
                $leaderboard_entry['leaderboard_id'] = $leaderboard_record['leaderboard_id'];
                $leaderboard_entry['steam_user_id'] = $steam_user_id;
                $leaderboard_entry['leaderboard_snapshot_id'] = $leaderboard_snapshot_id;
                
                $entry_record = array_merge($placeholder_record, $leaderboard_entry);
                
                db()->insert('leaderboard_entries', $entry_record, 'add_leaderboard_entry');  
            }
        }
    }
    
    protected static function getFormattedLeaderboardEntryRecord($unformatted_record) {    
        return array(
            'score' => $unformatted_record->score,
            'steamid' => $unformatted_record->steamid,
            'details' => $unformatted_record->details,
            'ugcid' => $unformatted_record->ugcid
        );
    }
    
    protected function populateSeasonLeaderboard($daily_ranking_season_id) {
        
    }
    
    public function saveLeaderboardEntries() {    
        $leaderboards = $this->cache->hGetAll(CacheEntryNames::LEADERBOARDS);
    
        if(!empty($leaderboards)) {
            foreach($leaderboards as $lbid => $leaderboard_hash_name) {   
                $leaderboard_record = $this->imported_leaderboards[$lbid];
    
                $leaderboard_id = $leaderboard_record['leaderboard_id'];
                
                $leaderboard_snapshot_id = db()->getOne("
                    SELECT leaderboard_snapshot_id
                    FROM leaderboard_snapshots
                    WHERE leaderboard_id = ?
                        AND date = ?
                ", array(
                    $leaderboard_id,
                    $this->current_date->format('Y-m-d')
                ));
                
                if(empty($leaderboard_snapshot_id)) {
                    if($this->verbose_output) {
                        $this->framework->coutLine("No existing leaderboard snapshot was found for leaderboard '{$leaderboard_record['name']}' for today. Creating a new one.");
                    }
                
                    $leaderboard_snapshot_id = db()->insert('leaderboard_snapshots', array(
                        'leaderboard_id' => $leaderboard_id,
                        'date' => $this->current_date->format('Y-m-d'),
                        'created' => date('Y-m-d H:i:s')
                    ), 'add_leaderboard_snapshot');
                }
                else {
                    if($this->verbose_output) {
                        $this->framework->coutLine("An existing snapshot was found for for leaderboard '{$leaderboard_record['name']}' for today. Deleting existing data to replace with new records.");
                    }
                
                    db()->update('leaderboard_snapshots', array(
                        'updated' => date('Y-m-d H:i:s')
                    ), array(
                        'leaderboard_snapshot_id' => $leaderboard_snapshot_id
                    ), array(), 'update_leaderboard_snapshot');
                
                    db()->delete('leaderboard_entries', array(
                        'leaderboard_snapshot_id' => $leaderboard_snapshot_id
                    ), array(), 'delete_leaderboard_entries');
                    
                    if($this->verbose_output) {
                        $this->framework->coutLine("Updating leaderboard with latest snapshot.");
                    }
                }
                
                if($this->verbose_output) {
                    $this->framework->coutLine("Linking leaderboard with snapshot.");
                }

                db()->update('leaderboards', array(
                    'last_snapshot_id' => $leaderboard_snapshot_id
                ), array(
                    'leaderboard_id' => $leaderboard_id
                ), array(), 'update_leaderboard_latest_snapshot');
    
                $this->imported_leaderboards[$lbid]['last_snapshot_id'] = $leaderboard_snapshot_id;
                
                if($this->verbose_output) {
                    $this->framework->coutLine("Saving snapshot entries for leaderboard '{$leaderboard_record['name']}'.");
                }
                
                $leaderboard_entries = $this->cache->zRange(CacheEntryNames::generateLeaderboardEntriesName($lbid), 0, -1, true);
                
                $local_transaction = $this->cache->transaction();
                
                $local_transaction->setCommitProcessCallback(array($this, 'processLeaderboardEntriesChunk'), array(
                    'lbid' => $lbid,
                    'leaderboard_snapshot_id' => $leaderboard_snapshot_id
                ));        
            
                foreach($leaderboard_entries as $leaderboard_entry_name => $rank) {                              
                    $local_transaction->hGetAll($leaderboard_entry_name);
                }
                
                $local_transaction->commit();
                
                if(!empty($this->latest_daily_season) && !empty($leaderboard_record['is_daily_ranking'])) {
                    $daily_date = new DateTime($leaderboard_record['daily_date']);
                    
                    $season_start_date = new DateTime($this->latest_daily_season['start_date']);
                    $season_end_date = new DateTime($this->latest_daily_season['end_date']);
                    
                    if($daily_date >= $season_start_date && $daily_date <= $season_end_date) {
                        DailyRankingSeasonLeaderboards::populateLeaderboard($this->latest_daily_season['daily_ranking_season_id'], $leaderboard_id, $daily_date);
                    }
                }
            }
        }
    }
    
    public function processPowerRankingEntriesChunk($power_ranking_entries, $power_ranking_id) {
        if(!empty($power_ranking_entries)) {       
            //This empty record will allow the same prepared statement to be reused for a potential performance gain.
            $empty_entry_record = array(
                'power_ranking_id' => NULL,
                'steam_user_id' => NULL,
                'cadence_score_rank' => NULL,
                'cadence_score_rank_points' => NULL,
                'cadence_score' => NULL,
                'bard_score_rank' => NULL,
                'bard_score_rank_points' => NULL,
                'bard_score' => NULL,
                'monk_score_rank' => NULL,
                'monk_score_rank_points' => NULL,
                'monk_score' => NULL,
                'aria_score_rank' => NULL,
                'aria_score_rank_points' => NULL,
                'aria_score' => NULL,
                'bolt_score_rank' => NULL,
                'bolt_score_rank_points' => NULL,
                'bolt_score' => NULL,
                'dove_score_rank' => NULL,
                'dove_score_rank_points' => NULL,
                'dove_score' => NULL,
                'eli_score_rank' => NULL,
                'eli_score_rank_points' => NULL,
                'eli_score' => NULL,
                'melody_score_rank' => NULL,
                'melody_score_rank_points' => NULL,
                'melody_score' => NULL,
                'dorian_score_rank' => NULL,
                'dorian_score_rank_points' => NULL,
                'dorian_score' => NULL,
                'coda_score_rank' => NULL,
                'coda_score_rank_points' => NULL,
                'coda_score' => NULL,
                'all_score_rank' => NULL,
                'all_score_rank_points' => NULL,
                'all_score' => NULL,
                'story_score_rank' => NULL,
                'story_score_rank_points' => NULL,
                'story_score' => NULL,
                'score_rank_points_total' => NULL,
                'cadence_deathless_score_rank' => NULL,
                'cadence_deathless_score_rank_points' => NULL,
                'cadence_deathless_score' => NULL,
                'bard_deathless_score_rank' => NULL,
                'bard_deathless_score_rank_points' => NULL,
                'bard_deathless_score' => NULL,
                'monk_deathless_score_rank' => NULL,
                'monk_deathless_score_rank_points' => NULL,
                'monk_deathless_score' => NULL,
                'aria_deathless_score_rank' => NULL,
                'aria_deathless_score_rank_points' => NULL,
                'aria_deathless_score' => NULL,
                'bolt_deathless_score_rank' => NULL,
                'bolt_deathless_score_rank_points' => NULL,
                'bolt_deathless_score' => NULL,
                'dove_deathless_score_rank' => NULL,
                'dove_deathless_score_rank_points' => NULL,
                'dove_deathless_score' => NULL,
                'eli_deathless_score_rank' => NULL,
                'eli_deathless_score_rank_points' => NULL,
                'eli_deathless_score' => NULL,
                'melody_deathless_score_rank' => NULL,
                'melody_deathless_score_rank_points' => NULL,
                'melody_deathless_score' => NULL,
                'dorian_deathless_score_rank' => NULL,
                'dorian_deathless_score_rank_points' => NULL,
                'dorian_deathless_score' => NULL,
                'coda_deathless_score_rank' => NULL,
                'coda_deathless_score_rank_points' => NULL,
                'coda_deathless_score' => NULL,
                'all_deathless_score_rank' => NULL,
                'all_deathless_score_rank_points' => NULL,
                'all_deathless_score' => NULL,
                'story_deathless_score_rank' => NULL,
                'story_deathless_score_rank_points' => NULL,
                'story_deathless_score' => NULL,
                'deathless_score_rank_points_total' => NULL,
                'cadence_speed_rank' => NULL,
                'cadence_speed_rank_points' => NULL,
                'cadence_speed_time' => NULL,
                'bard_speed_rank' => NULL,
                'bard_speed_rank_points' => NULL,
                'bard_speed_time' => NULL,
                'monk_speed_rank' => NULL,
                'monk_speed_rank_points' => NULL,
                'monk_speed_time' => NULL,
                'aria_speed_rank' => NULL,
                'aria_speed_rank_points' => NULL,
                'aria_speed_time' => NULL,
                'bolt_speed_rank' => NULL,
                'bolt_speed_rank_points' => NULL,
                'bolt_speed_time' => NULL,
                'dove_speed_rank' => NULL,
                'dove_speed_rank_points' => NULL,
                'dove_speed_time' => NULL,
                'eli_speed_rank' => NULL,
                'eli_speed_rank_points' => NULL,
                'eli_speed_time' => NULL,
                'melody_speed_rank' => NULL,
                'melody_speed_rank_points' => NULL,
                'melody_speed_time' => NULL,
                'dorian_speed_rank' => NULL,
                'dorian_speed_rank_points' => NULL,
                'dorian_speed_time' => NULL,
                'coda_speed_rank' => NULL,
                'coda_speed_rank_points' => NULL,
                'coda_speed_time' => NULL,
                'all_speed_rank' => NULL,
                'all_speed_rank_points' => NULL,
                'all_speed_time' => NULL,
                'story_speed_rank' => NULL,
                'story_speed_rank_points' => NULL,
                'story_speed_time' => NULL,   
                'speed_rank_points_total' => NULL,
                'cadence_deathless_speed_rank' => NULL,
                'cadence_deathless_speed_rank_points' => NULL,
                'cadence_deathless_speed_time' => NULL,
                'bard_deathless_speed_rank' => NULL,
                'bard_deathless_speed_rank_points' => NULL,
                'bard_deathless_speed_time' => NULL,
                'monk_deathless_speed_rank' => NULL,
                'monk_deathless_speed_rank_points' => NULL,
                'monk_deathless_speed_time' => NULL,
                'aria_deathless_speed_rank' => NULL,
                'aria_deathless_speed_rank_points' => NULL,
                'aria_deathless_speed_time' => NULL,
                'bolt_deathless_speed_rank' => NULL,
                'bolt_deathless_speed_rank_points' => NULL,
                'bolt_deathless_speed_time' => NULL,
                'dove_deathless_speed_rank' => NULL,
                'dove_deathless_speed_rank_points' => NULL,
                'dove_deathless_speed_time' => NULL,
                'eli_deathless_speed_rank' => NULL,
                'eli_deathless_speed_rank_points' => NULL,
                'eli_deathless_speed_time' => NULL,
                'melody_deathless_speed_rank' => NULL,
                'melody_deathless_speed_rank_points' => NULL,
                'melody_deathless_speed_time' => NULL,
                'dorian_deathless_speed_rank' => NULL,
                'dorian_deathless_speed_rank_points' => NULL,
                'dorian_deathless_speed_time' => NULL,
                'coda_deathless_speed_rank' => NULL,
                'coda_deathless_speed_rank_points' => NULL,
                'coda_deathless_speed_time' => NULL,
                'all_deathless_speed_rank' => NULL,
                'all_deathless_speed_rank_points' => NULL,
                'all_deathless_speed_time' => NULL,
                'story_deathless_speed_rank' => NULL,
                'story_deathless_speed_rank_points' => NULL,
                'story_deathless_speed_time' => NULL,
                'deathless_speed_rank_points_total' => NULL,
                'total_points' => NULL,
                'speed_rank' => NULL,
                'deathless_speed_rank' => NULL,
                'score_rank' => NULL,
                'deathless_score_rank' => NULL,
                'rank' => NULL,
            );
         
            foreach($power_ranking_entries as $power_ranking_entry) {
                $steamid = $power_ranking_entry['steamid'];
                unset($power_ranking_entry['steamid']);

                $steam_user_id = self::$users[$steamid];
                $power_rankings_entry_hash_name = CacheEntryNames::generatePowerRankingEntryName($steamid);
                                              
                $entry_record = array_merge($empty_entry_record, $power_ranking_entry);
                $entry_record['power_ranking_id'] = $power_ranking_id;
                $entry_record['steam_user_id'] = $steam_user_id;
                
                $entry_record['rank'] = $this->power_ranking_scores[$power_rankings_entry_hash_name];
                
                if(isset($this->score_ranking_scores[$power_rankings_entry_hash_name])) {
                    $entry_record['score_rank'] = $this->score_ranking_scores[$power_rankings_entry_hash_name] + 1;
                }
                
                if(isset($this->speed_ranking_scores[$power_rankings_entry_hash_name])) {
                    $entry_record['speed_rank'] = $this->speed_ranking_scores[$power_rankings_entry_hash_name] + 1;
                }
                
                if(isset($this->deathless_ranking_scores[$power_rankings_entry_hash_name])) {
                    $entry_record['deathless_score_rank'] = $this->deathless_ranking_scores[$power_rankings_entry_hash_name] + 1;
                }
                
                $power_ranking_entry_id = db()->insert('power_ranking_entries', $entry_record, 'power_ranking_entry');
                
                db()->update('steam_users', array(
                    'latest_power_ranking_entry_id' => $power_ranking_entry_id,
                ), array(
                    'steam_user_id' => $steam_user_id
                ), array(), 'update_steam_user');
            }
        }
    }
    
    protected function saveRankedPowerLeaderboards() {      
        if($this->verbose_output) {
            $this->framework->coutLine("Executing fourth pass to add finalized data into database.");
        }  
        
        $this->power_ranking_scores = $this->cache->zRevRange(CacheEntryNames::POWER_RANKING_TOTAL_POINTS, 0, -1); 
        
        if(!empty($this->power_ranking_scores)) {
            $this->power_ranking_scores = array_flip($this->power_ranking_scores);
        
            $this->score_ranking_scores = $this->cache->zRevRange(CacheEntryNames::POWER_RANKING_SCORE_POINTS, 0, -1);
            
            if(!empty($this->score_ranking_scores)) {
                $this->score_ranking_scores = array_flip($this->score_ranking_scores);
            }
            
            $this->speed_ranking_scores = $this->cache->zRevRange(CacheEntryNames::POWER_RANKING_SPEED_POINTS, 0, -1);
            
            if(!empty($this->speed_ranking_scores)) {
                $this->speed_ranking_scores = array_flip($this->speed_ranking_scores);
            }
            
            $this->deathless_ranking_scores = $this->cache->zRevRange(CacheEntryNames::POWER_RANKING_DEATHLESS_POINTS, 0, -1);
            
            if(!empty($this->deathless_ranking_scores)) {
                $this->deathless_ranking_scores = array_flip($this->deathless_ranking_scores);
            }
            
            if($this->verbose_output) {
                $this->framework->coutLine("Checking to see if there is an existing power ranking for today.");
            }
        
            $power_ranking_id = db()->getOne("
                SELECT power_ranking_id
                FROM power_rankings
                WHERE date = ?
            ", array(
                $this->current_date->format('Y-m-d')
            ));
            
            if(empty($power_ranking_id)) {
                if($this->verbose_output) {
                    $this->framework->coutLine("No power ranking in the database for today was found. Creating a new one.");
                }
                
                $power_ranking_id = db()->insert('power_rankings', array(
                    'date' => $this->current_date->format('Y-m-d'),
                    'created' => date('Y-m-d H:i:s')
                ));
            }
            else {
                if($this->verbose_output) {
                    $this->framework->coutLine("An existing power ranking in the database for today was found. Deleting existing entries to replace with new ones.");
                }
                
                db()->update('power_rankings', array(
                    'updated' => date('Y-m-d H:i:s')
                ), array(
                    'power_ranking_id' => $power_ranking_id
                ));
                
                db()->delete('power_ranking_entries', array(
                    'power_ranking_id' => $power_ranking_id
                ));
            }
            
            //Mark this new power ranking as the latest one
            db()->update('power_rankings', array(
                'latest' => 0
            ), array(
                'latest' => 1
            ));
            
            db()->update('power_rankings', array(
                'latest' => 1
            ), array(
                'power_ranking_id' => $power_ranking_id
            ));

            $local_transaction = $this->cache->transaction();
            
            $local_transaction->setCommitProcessCallback(array($this, 'processPowerRankingEntriesChunk'), array(
                'power_ranking_id' => $power_ranking_id
            ));
            
            foreach($this->power_ranking_scores as $power_ranking_entry_hash_name => $index) {
                $local_transaction->hGetAll($power_ranking_entry_hash_name);
            }
            
            $local_transaction->commit();
        }
    }
    
    protected function generateDailyRankingStats() {
        DailyRankings::loadDailyRankingDayTypesIntoCache($this->cache);
        DailyRankings::loadDailyLeaderboardsIntoCache($this->cache);
        DailyRankings::loadDailyLeaderboardEntriesIntoCache($this->cache);
        DailyRankingSeasonLeaderboards::loadLeaderboardsIntoCache($this->cache);
        DailyRankingSeasonLeaderboards::loadLeaderboardEntriesIntoCache($this->cache);
        
        $daily_ranking_generation_lua_script = file_get_contents($this->lua_script_directory_path . '/daily_ranking_generation.lua');
    
        $this->cache->eval($daily_ranking_generation_lua_script, array(
            CacheEntryNames::DAILY_LEADERBOARDS,
            CacheEntryNames::LEADERBOARD_ENTRY,
            CacheEntryNames::FILTER,
            CacheEntryNames::STEAM_USERS_BY_NAME,
            CacheEntryNames::DAILY_RANKINGS,
            CacheEntryNames::DAILY_RANKING_ENTRIES,
            CacheEntryNames::DAILY_RANKING_TOTAL_POINTS,
            CacheEntryNames::STEAM_USERS_BY_NAME,
            CacheEntryNames::TEMP_NAME,
            CacheEntryNames::STEAM_USERS
        ));
        
        if(!empty($this->daily_ranking_day_types)) {
            foreach($this->daily_ranking_day_types as $daily_ranking_day_type) {
                $number_of_days = $daily_ranking_day_type['number_of_days'];

                $this->cache->eval($daily_ranking_generation_lua_script, array(
                    CacheEntryNames::generateDailyDayTypeLeaderboardsName($number_of_days),
                    CacheEntryNames::LEADERBOARD_ENTRY,
                    CacheEntryNames::FILTER,
                    CacheEntryNames::STEAM_USERS_BY_NAME,
                    CacheEntryNames::generateDailyDayTypeRankingName($number_of_days),
                    CacheEntryNames::generateDailyDayTypeRankingEntriesName($number_of_days),
                    CacheEntryNames::generateDailyDayTypeRankingTotalPointsName($number_of_days),
                    CacheEntryNames::STEAM_USERS_BY_NAME,
                    CacheEntryNames::TEMP_NAME,
                    CacheEntryNames::STEAM_USERS
                ));
            }
        }
        
        if(!empty($this->latest_daily_season)) {
            $this->cache->eval($daily_ranking_generation_lua_script, array(
                CacheEntryNames::DAILY_SEASON_LEADERBOARD_ENTRIES,
                CacheEntryNames::LEADERBOARD_ENTRY,
                CacheEntryNames::FILTER,
                CacheEntryNames::STEAM_USERS_BY_NAME,
                CacheEntryNames::DAILY_SEASON_RANKINGS,
                CacheEntryNames::DAILY_SEASON_RANKING_ENTRIES,
                CacheEntryNames::DAILY_SEASON_RANKING_TOTAL_POINTS,
                CacheEntryNames::STEAM_USERS_BY_NAME,
                CacheEntryNames::TEMP_NAME,
                CacheEntryNames::STEAM_USERS
            ));
        }
    }
    
    public function processDailyEntriesChunk($daily_ranking_entries, $daily_ranking_id) {
        $empty_daily_ranking_entry_record = array(
            'daily_ranking_id' => NULL,
            'steam_user_id' => NULL,
            'first_place_ranks' => NULL,
            'top_5_ranks' => NULL,
            'top_10_ranks' => NULL,
            'top_20_ranks' => NULL,
            'top_50_ranks' => NULL,
            'top_100_ranks' => NULL,
            'total_points' => NULL,
            'points_per_day' => NULL,
            'total_dailies' => NULL,
            'total_wins' => NULL,
            'average_rank' => NULL,
            'sum_of_ranks' => NULL,
            'rank' => NULL
        );
        
        if(!empty($daily_ranking_entries)) {
            foreach($daily_ranking_entries as $daily_ranking_entry) {
                $steamid = $daily_ranking_entry['steamid'];
                unset($daily_ranking_entry['steamid']);
            
                $daily_ranking_record = array_merge($empty_daily_ranking_entry_record, $daily_ranking_entry);
                
                $steam_user_id = NULL;
                
                if(!empty(self::$users[$steamid])) {
                    $steam_user_id = self::$users[$steamid];
                }
                else {                
                    $steam_user_id = db()->insert('steam_users', array(
                        'steamid' => $steamid
                    ));
                    
                    self::$users[$steamid] = $steam_user_id;
                }
                
                $daily_ranking_record['steam_user_id'] = $steam_user_id;
                $daily_ranking_record['daily_ranking_id'] = $daily_ranking_id;

                $daily_ranking_entry_id = db()->insert('daily_ranking_entries', $daily_ranking_record, 'add_daily_entry');
                
                db()->delete('steam_users_latest_daily_rankings', array(
                    'steam_user_id' => $steam_user_id,
                    'daily_ranking_id' => $daily_ranking_id
                ));
                
                db()->insert('steam_users_latest_daily_rankings', array(
                    'steam_user_id' => $steam_user_id,
                    'daily_ranking_id' => $daily_ranking_id,
                    'daily_ranking_entry_id' => $daily_ranking_entry_id
                ));
            }
        }
    }
    
    public function processDailySeasonEntriesChunk($daily_ranking_season_entries, $daily_ranking_season_id, $daily_ranking_season_snapshot_id) {
        $empty_daily_ranking_entry_record = array(
            'daily_ranking_id' => NULL,
            'steam_user_id' => NULL,
            'first_place_ranks' => NULL,
            'top_5_ranks' => NULL,
            'top_10_ranks' => NULL,
            'top_20_ranks' => NULL,
            'top_50_ranks' => NULL,
            'top_100_ranks' => NULL,
            'total_points' => NULL,
            'points_per_day' => NULL,
            'total_dailies' => NULL,
            'total_wins' => NULL,
            'average_place' => NULL,
            'sum_of_ranks' => NULL,
            'number_of_ranks' => NULL,
            'rank' => NULL
        );
        
        if(!empty($daily_ranking_entries)) {
            foreach($daily_ranking_entries as &$daily_ranking_entry) {
                $steamid = $daily_ranking_entry['steamid'];
                unset($daily_ranking_entry['steamid']);

                $steam_user_id = NULL;
                
                if(!empty(self::$users[$steamid])) {
                    $steam_user_id = self::$users[$steamid];
                }
                else {                
                    $steam_user_id = db()->insert('steam_users', array(
                        'steamid' => $steamid
                    ));
                    
                    self::$users[$steamid] = $steam_user_id;
                }
                
                $daily_ranking_record['steam_user_id'] = $steam_user_id;
            
                $daily_ranking_record = array_merge($empty_daily_ranking_entry_record, $daily_ranking_entries);

                db()->insert('daily_ranking_entries', $daily_ranking_record, 'add_daily_entry');
            }
        }
    }
    
    protected function saveDailyRankingDayTypeStats($daily_ranking_day_type_id = NULL) {
        $daily_ranking_entries_name = CacheEntryNames::DAILY_RANKING_ENTRIES;
        
        if(!empty($daily_ranking_day_type_id)) {
            $daily_ranking_day_type = $this->daily_ranking_day_types[$daily_ranking_day_type_id];
        
            $daily_ranking_entries_name = CacheEntryNames::generateDailyDayTypeRankingEntriesName($daily_ranking_day_type['number_of_days']);
        }
    
        $daily_ranking_entries = $this->cache->zRange($daily_ranking_entries_name, 0, -1, true);
        
        if(!empty($daily_ranking_entries)) {
            $daily_ranking_query = "
                SELECT daily_ranking_id
                FROM daily_rankings
                WHERE date = :date
            ";
            
            $daily_ranking_query_placeholder_values = array(
                ':date' => $this->current_date->format('Y-m-d')
            );
            
            $ranking_day_type_query_criteria = '';
            $ranking_day_type_placeholder_values = array();
            
            if(empty($daily_ranking_day_type_id)) {
                $ranking_day_type_query_criteria = "daily_ranking_day_type_id IS NULL";
            }
            else {
                $ranking_day_type_query_criteria = "daily_ranking_day_type_id = :daily_ranking_day_type_id";
                
                $daily_ranking_query_placeholder_values[':daily_ranking_day_type_id'] = $daily_ranking_day_type_id;
                $ranking_day_type_placeholder_values[':daily_ranking_day_type_id'] = $daily_ranking_day_type_id;
            }
            
            $daily_ranking_query .= "\nAND {$ranking_day_type_query_criteria}";
        
            $daily_ranking_id = db()->getOne($daily_ranking_query, $daily_ranking_query_placeholder_values);
            
            if(empty($daily_ranking_id)) {
                if($this->verbose_output) {
                    $this->framework->coutLine("No existing daily ranking snapshot was found for for today. Creating daily rankings for today.");
                }
                
                db()->query("
                    UPDATE daily_rankings
                    SET latest = 0
                    WHERE {$ranking_day_type_query_criteria}
                ", $ranking_day_type_placeholder_values);
            
                $daily_ranking_id = db()->insert('daily_rankings', array(
                    'latest' => 1,
                    'date' => $this->current_date->format('Y-m-d'),
                    'daily_ranking_day_type_id' => $daily_ranking_day_type_id,
                    'created' => date('Y-m-d H:i:s')
                ));
            }
            else {
                if($this->verbose_output) {
                    $this->framework->coutLine("An existing daily ranking snapshot was found for today. Deleting existing data to replace with new records.");
                }
            
                db()->update('daily_rankings', array(
                    'updated' => date('Y-m-d H:i:s')
                ), array(
                    'daily_ranking_id' => $daily_ranking_id
                ));
            
                db()->delete('daily_ranking_entries', array(
                    'daily_ranking_id' => $daily_ranking_id
                ));
            }
        
            $local_transaction = $this->cache->transaction();
                    
            $local_transaction->setCommitProcessCallback(array($this, 'processDailyEntriesChunk'), array(
                'daily_ranking_id' => $daily_ranking_id
            ));        
        
            foreach($daily_ranking_entries as $daily_ranking_entry_name => $rank) {                              
                $local_transaction->hGetAll($daily_ranking_entry_name);
            }
            
            $local_transaction->commit();
        }
    }
    
    protected function saveDailyRankingStats() {
        /* ---------- Regular rankings ---------- */        
        $this->saveDailyRankingDayTypeStats();
        
        if(!empty($this->daily_ranking_day_types)) {
            foreach($this->daily_ranking_day_types as $daily_ranking_day_type) {                
                $this->saveDailyRankingDayTypeStats($daily_ranking_day_type['daily_ranking_day_type_id']);
            }
        }
        
        /* ---------- Daily Ranking Seasons ---------- */
            
        if(!empty($this->latest_daily_season)) {             
            $daily_ranking_season_id = $this->latest_daily_season['daily_ranking_season_id'];
        
            $daily_ranking_season_entries = $this->cache->zRange(CacheEntryNames::DAILY_SEASON_RANKING_ENTRIES, 0, -1, true);
        
            if(!empty($daily_ranking_season_entries)) {
                $daily_ranking_season_snapshot_id = db()->getOne("
                    SELECT daily_ranking_season_snapshot_id
                    FROM daily_ranking_season_snapshots
                    WHERE daily_ranking_season_id = ?
                        AND date = ?
                ", array(
                    $daily_ranking_season_id,
                    $this->current_date->format('Y-m-d')
                ));
                
                if(empty($daily_ranking_season_snapshot_id)) {
                    if($this->verbose_output) {
                        $this->framework->coutLine("No existing daily ranking season snapshot was found for today. Creating daily rankings for today.");
                    }
                    
                    db()->update('daily_ranking_season_snapshots', array(
                        'is_latest' => 0
                    ), array(
                        'daily_ranking_season_id' => $daily_ranking_season_id
                    ));
                
                    $daily_ranking_season_snapshot_id = db()->insert('daily_ranking_season_snapshots', array(
                        'daily_ranking_season_id' => $daily_ranking_season_id,
                        'is_latest' => 1,
                        'date' => $this->current_date->format('Y-m-d'),
                        'created' => date('Y-m-d H:i:s')
                    ));
                }
                else {
                    if($this->verbose_output) {
                        $this->framework->coutLine("An existing daily ranking season snapshot was found for today. Deleting existing data to replace with new records.");
                    }
                
                    db()->update('daily_ranking_season_snapshots', array(
                        'updated' => date('Y-m-d H:i:s')
                    ), array(
                        'daily_ranking_season_snapshot_id' => $daily_ranking_season_snapshot_id
                    ));
                
                    db()->delete('daily_ranking_season_entries', array(
                        'daily_ranking_season_snapshot_id' => $daily_ranking_season_snapshot_id
                    ));
                }
            
                $local_transaction = $this->cache->transaction();
                        
                $local_transaction->setCommitProcessCallback(array($this, 'processDailySeasonEntriesChunk'), array(
                    'daily_ranking_season_id' => $daily_ranking_season_id,
                    'daily_ranking_season_snapshot_id' => $daily_ranking_season_snapshot_id
                ));        
            
                foreach($daily_ranking_season_entries as $daily_ranking_season_entry_name => $rank) {                              
                    $local_transaction->hGetAll($daily_ranking_season_entry_name);
                }
                
                $local_transaction->commit();
            }
        }
    }
}