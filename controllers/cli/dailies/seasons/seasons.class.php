<?php
namespace Modules\Necrolab\Controllers\Cli\Dailies\Seasons;

use \DateTime;
use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\DailySeasons\DailyRankings;
use \Modules\Necrolab\Models\DailyRankings\Cache\DailyRankings;
use \Modules\Necrolab\Models\DailyRankings\Cache\CacheNames;

class Seasons
extends Cli { 
    protected $cache;
    
    public function init() {
        $this->cache = cache('write');
    }
    
    protected function renameTempKeys($number_of_days = NULL) {        
        $total_points_name = CacheNames::getTotalPointsName($number_of_days);
        $entries_name = CacheNames::getEntriesName($number_of_days);
        
        $transaction = $this->cache->transaction();
        
        $transaction->rename(CacheNames::addTempPrefix($total_points_name), $total_points_name);
        $transaction->rename(CacheNames::addTempPrefix($entries_name), $entries_name);
        
        $rank_sum_hash_name = CacheNames::getRankSumName($number_of_days);
        $rank_sum_temp_hash_name = CacheNames::addTempPrefix($rank_sum_hash_name);
        
        $transaction->del($rank_sum_temp_hash_name);
        
        $total_dailies_hash_name = CacheNames::getRankSumName($number_of_days);
        $total_dailies_temp_hash_name = CacheNames::addTempPrefix($total_dailies_hash_name);
        
        $transaction->del($total_dailies_temp_hash_name);
        
        $transaction->commit();
    }
    
    protected function generateRanksFromPoints($number_of_days = NULL) {        
        $daily_ranking_total_points_temp_name = CacheNames::addTempPrefix(CacheNames::getTotalPointsName($number_of_days));
        $daily_ranking_entries_temp_name = CacheNames::addTempPrefix(CacheNames::getEntriesName($number_of_days));
        
        $total_points_entries = $this->cache->zRevRange($daily_ranking_total_points_temp_name, 0, -1);
        
        $transaction = $this->cache->transaction();
        
        foreach($total_points_entries as $rank => $daily_ranking_entry_hash_name) {        
            $daily_ranking_entry_temp_hash_name = CacheNames::addTempPrefix($daily_ranking_entry_hash_name);
            
            $transaction->zAdd($daily_ranking_entries_temp_name, $rank, $daily_ranking_entry_hash_name);
            $transaction->hSet($daily_ranking_entry_temp_hash_name, 'rank', $rank);
            $transaction->rename($daily_ranking_entry_temp_hash_name, $daily_ranking_entry_hash_name);
        }
        
        $transaction->commit();
    }
    
    protected function generateRankStats($number_of_days = NULL) {
        $total_points_hash_name = CacheNames::getTotalPointsName($number_of_days);
        $total_points_temp_hash_name = CacheNames::addTempPrefix($total_points_hash_name);
        
        $rank_sum_hash_name = CacheNames::getRankSumName($number_of_days);
        $rank_sum_temp_hash_name = CacheNames::addTempPrefix($rank_sum_hash_name);
        
        $total_dailies_hash_name = CacheNames::getRankSumName($number_of_days);
        $total_dailies_temp_hash_name = CacheNames::addTempPrefix($total_dailies_hash_name);
        
        $total_points_entries = $this->cache->zRevRange($total_points_temp_hash_name, 0, -1, true);
        $rank_sum_entries = $this->cache->hGetAll($rank_sum_temp_hash_name);
        $total_dailies_entries = $this->cache->hGetAll($total_dailies_temp_hash_name);
        
        $transaction = $this->cache->transaction();
        
        foreach($total_points_entries as $daily_ranking_entry_hash_name => $total_points) {        
            $daily_ranking_entry_temp_hash_name = CacheNames::addTempPrefix($daily_ranking_entry_hash_name);
            
            $rank_sum = $rank_sum_entries[$daily_ranking_entry_temp_hash_name];
            $total_dailies = $total_dailies_entries[$daily_ranking_entry_temp_hash_name];
            
            $points_per_day = $total_points / $total_dailies;
            $average_rank = $rank_sum / $total_dailies;
            
            $transaction->hMSet($daily_ranking_entry_temp_hash_name, array(
                'points_per_day' => $points_per_day,
                'average_rank' => $average_rank
            ));
        }
        
        $transaction->commit();
    }
    
    public function processLeaderboardEntriesChunk($leaderboard_entries, $leaderboard_record, $number_of_days) {
        if(!empty($leaderboard_entries)) {
            $transaction = $this->cache->transaction();
            
            $total_points_hash_name = CacheNames::getTotalPointsName($number_of_days);
            $total_points_temp_hash_name = CacheNames::addTempPrefix($total_points_hash_name);
            
            $rank_sum_hash_name = CacheNames::getRankSumName($number_of_days);
            $rank_sum_temp_hash_name = CacheNames::addTempPrefix($rank_sum_hash_name);
            
            $total_dailies_hash_name = CacheNames::getRankSumName($number_of_days);
            $total_dailies_temp_hash_name = CacheNames::addTempPrefix($total_dailies_hash_name);

            foreach($leaderboard_entries as $leaderboard_entry) {                
                $leaderboard_entry_record = new ScoreEntry();

                $leaderboard_entry_record->setPropertiesFromIndexedArray($leaderboard_entry);
                
                $steamid = $leaderboard_entry_record->steamid;
                
                $daily_ranking_entry_hash_name = CacheNames::getEntryName($leaderboard_entry_record->steamid, $number_of_days);
                $daily_ranking_entry_temp_hash_name = CacheNames::addTempPrefix($daily_ranking_entry_hash_name);
                
                $transaction->hSetNx($daily_ranking_entry_temp_hash_name, 'steamid', $steamid);
                
                $rank = $leaderboard_entry_record->rank;
                
                if($rank == 1) {
                    $transaction->hIncrBy($daily_ranking_entry_temp_hash_name, 'first_place_ranks', 1);
                }
                elseif($rank <= 5) {                    
                    $transaction->hIncrBy($daily_ranking_entry_temp_hash_name, 'top_5_ranks', 1);
                }
                elseif($rank <= 10) {                    
                    $transaction->hIncrBy($daily_ranking_entry_temp_hash_name, 'top_10_ranks', 1);
                }
                elseif($rank <= 20) {                    
                    $transaction->hIncrBy($daily_ranking_entry_temp_hash_name, 'top_20_ranks', 1);
                }
                elseif($rank <= 50) {                    
                    $transaction->hIncrBy($daily_ranking_entry_temp_hash_name, 'top_50_ranks', 1);
                }
                elseif($rank <= 100) {                    
                    $transaction->hIncrBy($daily_ranking_entry_temp_hash_name, 'top_100_ranks', 1);
                }
                
                $rank_points = 1.7 / (log($rank / 100 + 1.03) / log(10));
                
                $transaction->hIncrByFloat($daily_ranking_entry_temp_hash_name, 'total_points', $rank_points);
                $transaction->zIncrBy($total_points_temp_hash_name, $rank_points, $daily_ranking_entry_hash_name);
                
                if($leaderboard_entry_record->is_win == 1) {
                    $transaction->hIncrBy($daily_ranking_entry_temp_hash_name, 'total_wins', 1);
                }
                
                $transaction->hIncrBy($daily_ranking_entry_temp_hash_name, 'sum_of_ranks', $rank);
                $transaction->hIncrBy($rank_sum_temp_hash_name, $daily_ranking_entry_temp_hash_name, $rank);
                
                $transaction->hIncrBy($daily_ranking_entry_temp_hash_name, 'total_dailies', 1);
                $transaction->hIncrBy($total_dailies_temp_hash_name, $daily_ranking_entry_temp_hash_name, 1);
            }
            
            $transaction->commit();
        }
    }
    
    public function processLeaderboardsChunk($leaderboards, $number_of_days) {
        if(!empty($leaderboards)) {
            foreach($leaderboards as $leaderboard) {                
                if(!empty($leaderboard)) {
                    $leaderboard_record = new Leaderboard();
                    $leaderboard_record->setPropertiesFromIndexedArray($leaderboard);
                    
                    $lbid = $leaderboard_record->lbid;
                
                    $number_of_entries = $this->cache->getConnectionObject()->get(CacheNames::getLeaderboardEntriesName($lbid));
                
                    $transaction = $this->cache->transaction();
                    
                    $transaction->setCommitProcessCallback(array($this, 'processLeaderboardEntriesChunk'), array(
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
    
    protected function generateDayTypeRankings($number_of_days = NULL) {        
        $daily_leaderboards = $this->cache->hGetAll(CacheNames::getRankingsName($number_of_days));

        $transaction = $this->cache->transaction();
        
        $transaction->setCommitProcessCallback(array($this, 'processLeaderboardsChunk'), array(
            'number_of_days' => $number_of_days
        ));
        
        if(!empty($daily_leaderboards)) {
            foreach($daily_leaderboards as $lbid => $leaderboard_key_name) {
                $transaction->lRange($leaderboard_key_name, 0, -1);
            }
        }
        
        $transaction->commit();
        
        $this->generateRankStats($number_of_days);
        $this->generateRanksFromPoints($number_of_days);
        $this->renameTempKeys($number_of_days);
    }
    
    public function actionGenerateByDate($date = NULL) {
        $date = new DateTime($date);
    
        $day_types = $this->cache->hGetAll(CacheNames::getDayTypesName());
        
        if(!empty($day_types)) {
            foreach($day_types as $number_of_days => $day_type_id) {
                $this->generateDayTypeRankings($number_of_days);
            }
        }
        
        //Generate the default all time rankings
        $this->generateDayTypeRankings();
    }
}