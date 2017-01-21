<?php
namespace Modules\Necrolab\Controllers\Cli;

use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\Rankings\Cache\Speed\Rankings as SpeedRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Score\Rankings as ScoreRankings;
use \Modules\Necrolab\Models\Rankings\Cache\Deathless\Rankings as DeathlessRankings;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames as RankingCacheNames;
use \Modules\Necrolab\Models\Leaderboards\Cache\CacheNames as LeaderboardCacheNames;
use \Modules\Necrolab\Models\Leaderboards\Cache\RecordModels\Leaderboard;
use \Modules\Necrolab\Models\Leaderboards\Cache\RecordModels\ScoreEntry;
use \Modules\Necrolab\Models\Leaderboards\Cache\RecordModels\SpeedEntry;
use \Modules\Necrolab\Models\Leaderboards\Cache\RecordModels\DeathlessEntry;
use \Modules\Necrolab\Models\Rankings\Cache\Power\RecordModels\PowerRankingEntry;

class Rankings
extends Cli { 
    protected $cache;
    
    public function init() {
        $this->cache = cache('write');
    }
    
    protected function generatePowerRanksFromPoints() {    
        $power_ranking_total_points_temp_name = RankingCacheNames::addTempPrefix(RankingCacheNames::getPowerTotalPointsName());
        $power_ranking_entries_temp_name = RankingCacheNames::addTempPrefix(RankingCacheNames::getPowerEntriesName());
        
        $total_points_entries = $this->cache->zRevRange($power_ranking_total_points_temp_name, 0, -1);
        
        $transaction = $this->cache->transaction();
        
        foreach($total_points_entries as $rank => $power_ranking_entry_hash_name) { 
            $real_rank = $rank + 1;
        
            $power_ranking_entry_temp_hash_name = RankingCacheNames::addTempPrefix($power_ranking_entry_hash_name);
            
            $transaction->zAdd($power_ranking_entries_temp_name, $real_rank, $power_ranking_entry_hash_name);
            $transaction->hSet($power_ranking_entry_temp_hash_name, 'rank', $real_rank);
        }
        
        $transaction->commit();
    }
    
    protected function generateSpeedRanksFromPoints() {    
        $total_points_temp_name = RankingCacheNames::addTempPrefix(CacheNames::getSpeedPointsName());
        $entries_temp_name = RankingCacheNames::addTempPrefix(CacheNames::getSpeedEntriesName());
        
        $total_points_entries = $this->cache->zRevRange($total_points_temp_name, 0, -1);
        
        $transaction = $this->cache->transaction();
        
        foreach($total_points_entries as $rank => $entry_hash_name) {        
            $real_rank = $rank + 1;
        
            $entry_temp_hash_name = RankingCacheNames::addTempPrefix($entry_hash_name);
            
            $transaction->zAdd($entries_temp_name, $real_rank, $entry_hash_name);
            $transaction->hSet($entry_temp_hash_name, 'speed_rank', $real_rank);
        }
        
        $transaction->commit();
    }
    
    protected function generateScoreRanksFromPoints() {    
        $total_points_temp_name = RankingCacheNames::addTempPrefix(CacheNames::getScorePointsName());
        $entries_temp_name = RankingCacheNames::addTempPrefix(CacheNames::getScoreEntriesName());
        
        $total_points_entries = $this->cache->zRevRange($total_points_temp_name, 0, -1);
        
        $transaction = $this->cache->transaction();
        
        foreach($total_points_entries as $rank => $entry_hash_name) {      
            $real_rank = $rank + 1;
        
            $entry_temp_hash_name = RankingCacheNames::addTempPrefix($entry_hash_name);
            
            $transaction->zAdd($entries_temp_name, $real_rank, $entry_hash_name);
            $transaction->hSet($entry_temp_hash_name, 'score_rank', $real_rank);
        }
        
        $transaction->commit();
    }
    
    protected function generateDeathlessRanksFromPoints() {    
        $total_points_temp_name = RankingCacheNames::addTempPrefix(CacheNames::getDeathlessPointsName());
        $entries_temp_name = RankingCacheNames::addTempPrefix(CacheNames::getDeathlessEntriesName());
        
        $total_points_entries = $this->cache->zRevRange($total_points_temp_name, 0, -1);
        
        $transaction = $this->cache->transaction();
        
        foreach($total_points_entries as $rank => $entry_hash_name) {  
            $real_rank = $rank + 1;
        
            $entry_temp_hash_name = RankingCacheNames::addTempPrefix($entry_hash_name);
            
            $transaction->zAdd($entries_temp_name, $real_rank, $entry_hash_name);
            $transaction->hSet($entry_temp_hash_name, 'deathless_score_rank', $real_rank);
        }
        
        $transaction->commit();
    }
    
    protected function renamePowerTempKeys() {
        $total_points_name = RankingCacheNames::getPowerTotalPointsName();
        $total_points_temp_name = RankingCacheNames::addTempPrefix($total_points_name);
        
        $entries_name = RankingCacheNames::getPowerEntriesName();
        $entries_temp_name = RankingCacheNames::addTempPrefix($entries_name);
        
        $total_points_entries = $this->cache->zRevRange($total_points_temp_name, 0, -1);
        
        $transaction = $this->cache->transaction();
        
        foreach($total_points_entries as $rank => $entry_hash_name) {        
            $entry_temp_hash_name = RankingCacheNames::addTempPrefix($entry_hash_name);

            $transaction->rename($entry_temp_hash_name, $entry_hash_name);
        }
        
        $transaction->rename($total_points_temp_name, $total_points_name);
        $transaction->rename($entries_temp_name, $entries_name);
        
        $transaction->commit();        
    }
    
    protected function renameSpeedTempKeys() {
        $total_points_name = RankingCacheNames::getSpeedPointsName();
        $speed_entries_name = RankingCacheNames::getSpeedEntriesName();
        
        $this->cache->rename(RankingCacheNames::addTempPrefix($total_points_name), $total_points_name);
        $this->cache->rename(RankingCacheNames::addTempPrefix($speed_entries_name), $speed_entries_name);
    }
    
    protected function renameScoreTempKeys() {
        $total_points_name = RankingCacheNames::getScorePointsName();
        $score_entries_name = RankingCacheNames::getScoreEntriesName();
        
        $this->cache->rename(RankingCacheNames::addTempPrefix($total_points_name), $total_points_name);
        $this->cache->rename(RankingCacheNames::addTempPrefix($score_entries_name), $score_entries_name);
    }
    
    protected function renameDeathlessTempKeys() {
        $total_points_name = RankingCacheNames::getDeathlessPointsName();
        $deathless_entries_name = RankingCacheNames::getDeathlessEntriesName();
        
        $this->cache->rename(RankingCacheNames::addTempPrefix($total_points_name), $total_points_name);
        $this->cache->rename(RankingCacheNames::addTempPrefix($deathless_entries_name), $deathless_entries_name);
    }
    
    public function processLeaderboardEntriesChunk($leaderboard_entries, $leaderboard_record) {
        if(!empty($leaderboard_entries)) {
            $transaction = $this->cache->transaction();
        
            foreach($leaderboard_entries as $leaderboard_entry) {
                if(!empty($leaderboard_entry)) {
                    $leaderboard_entry_record = NULL;
                    
                    if($leaderboard_record->is_speedrun == 1) {
                        $leaderboard_entry_record = new SpeedEntry();
                    }
                    elseif($leaderboard_record->is_deathless == 1) {
                        $leaderboard_entry_record = new DeathlessEntry();
                    }
                    elseif($leaderboard_record->is_score_run == 1) {
                        $leaderboard_entry_record = new ScoreEntry();
                    }

                    $leaderboard_entry_record->setPropertiesFromIndexedArray($leaderboard_entry);

                    $power_ranking_entry_hash_name = RankingCacheNames::getPowerRankingEntryName($leaderboard_entry_record->steamid);
                    $power_ranking_entry_temp_hash_name = RankingCacheNames::addTempPrefix($power_ranking_entry_hash_name);
                    
                    $character_column_prefix = $leaderboard_record->character_name;
                    $total_points_column_name = '';
                    $rank_column_name = '';
                    $rank_points_column_name = '';
                    
                    $power_ranking_entry_record = new PowerRankingEntry();                
                    
                    if($leaderboard_record->is_speedrun == 1) {
                        $rank_column_name = "{$character_column_prefix}_speed_rank";
                        $rank_points_column_name = "{$character_column_prefix}_speed_rank_points";
                        $total_points_column_name = 'speed_rank_points_total';
                        
                        $stat_record_name = "{$character_column_prefix}_speed_time";
                        $power_ranking_entry_record->$stat_record_name = $leaderboard_entry_record->time;
                    }
                    elseif($leaderboard_record->is_deathless == 1) {
                        $rank_column_name = "{$character_column_prefix}_deathless_score_rank"; 
                        $rank_points_column_name = "{$character_column_prefix}_deathless_score_rank_points";
                        $total_points_column_name = 'deathless_score_rank_points_total';
                        
                        $stat_record_name = "{$character_column_prefix}_deathless_score";
                        $power_ranking_entry_record->$stat_record_name = $leaderboard_entry_record->score;
                    }
                    elseif($leaderboard_record->is_score_run == 1) {
                        $rank_column_name = "{$character_column_prefix}_score_rank";
                        $rank_points_column_name = "{$character_column_prefix}_score_rank_points";
                        $total_points_column_name = 'score_rank_points_total';
                        
                        $stat_record_name = "{$character_column_prefix}_score";
                        $power_ranking_entry_record->$stat_record_name = $leaderboard_entry_record->score;
                    }
                    
                    $rank = $leaderboard_entry_record->rank;
                    $rank_points = 1.7 / (log($rank / 100 + 1.03) / log(10));
                    
                    $power_ranking_entry_record->steamid = $leaderboard_entry_record->steamid;
                    $power_ranking_entry_record->$rank_column_name = $rank;
                    $power_ranking_entry_record->$rank_points_column_name = $rank_points;
                    
                    $transaction->hIncrByFloat($power_ranking_entry_temp_hash_name, $total_points_column_name, $rank_points);
                    $transaction->hIncrByFloat($power_ranking_entry_temp_hash_name, 'total_points', $rank_points);
                    $transaction->zIncrBy(RankingCacheNames::addTempPrefix(RankingCacheNames::getPowerTotalPointsName()), $rank_points, $power_ranking_entry_hash_name);
                    
                    if($leaderboard_record->is_deathless == 1) {
                        $transaction->zIncrBy(RankingCacheNames::addTempPrefix(RankingCacheNames::getDeathlessPointsName()), $rank_points, $power_ranking_entry_hash_name);
                    }
                    elseif($leaderboard_record->is_score_run == 1) {
                        $transaction->zIncrBy(RankingCacheNames::addTempPrefix(RankingCacheNames::getScorePointsName()), $rank_points, $power_ranking_entry_hash_name);
                    }
                    elseif($leaderboard_record->is_speedrun == 1) {
                        $transaction->zIncrBy(RankingCacheNames::addTempPrefix(RankingCacheNames::getSpeedPointsName()), $rank_points, $power_ranking_entry_hash_name);
                    }
                    
                    $transaction->hMSet($power_ranking_entry_temp_hash_name, $power_ranking_entry_record->toArray(false));
                }
            }
            
            $transaction->commit();
        }
    }
    
    public function processLeaderboardsChunk($leaderboards) {
        if(!empty($leaderboards)) {
            foreach($leaderboards as $leaderboard) {                
                if(!empty($leaderboard)) {
                    $leaderboard_record = new Leaderboard();
                    $leaderboard_record->setPropertiesFromIndexedArray($leaderboard);
                    
                    $lbid = $leaderboard_record->lbid;
                
                    $number_of_entries = $this->cache->getConnectionObject()->get(LeaderboardCacheNames::getEntriesName($lbid));
                
                    $transaction = $this->cache->transaction();
                    
                    $transaction->setCommitProcessCallback(array($this, 'processLeaderboardEntriesChunk'), array(
                        'leaderboard_record' => $leaderboard_record
                    ));
                    
                    for($entry_number = 1; $entry_number <= $number_of_entries; $entry_number++) {
                        $transaction->lRange(LeaderboardCacheNames::getEntryName($lbid, $entry_number), 0, -1);
                    }
                    
                    $transaction->commit();
                }
            }
        }
    }
    
    public function actionGenerate($date = NULL) {
        $date = new DateTime($date);
    
        $power_leaderboards = $this->cache->sMembers(LeaderboardCacheNames::getPowerLeaderboardsName());
        
        $transaction = $this->cache->transaction();
        $transaction->setCommitProcessCallback(array($this, 'processLeaderboardsChunk'));
        
        if(!empty($power_leaderboards)) {
            foreach($power_leaderboards as $leaderboard_key_name) {
                $transaction->lRange($leaderboard_key_name, 0, -1);
            }
        }
        
        $transaction->commit();
        
        $this->generatePowerRanksFromPoints();
        $this->generateSpeedRanksFromPoints();
        $this->generateScoreRanksFromPoints();
        $this->generateDeathlessRanksFromPoints();
        
        $this->renamePowerTempKeys();
        $this->renameSpeedTempKeys();
        $this->renameScoreTempKeys();
        $this->renameDeathlessTempKeys();
    }
}