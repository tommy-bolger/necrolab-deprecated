<?php
namespace Modules\Necrolab\Models\Rankings;

use \Modules\Necrolab\Models\Necrolab;

class Entry
extends Necrolab {
    public static function getFormattedApiRecord($active_characters, $data_row) {
        $processed_row = array();
        
        $score_total = NULL;
        $speed_total_time = NULL;
        $deathless_total_win_count = NULL;
        
        $total_points = 0;
        $total_score_points = NULL;
        $total_speed_points = NULL;
        $total_deathless_points = NULL;
    
        foreach($active_characters as $active_character) {
            $name = $active_character['name'];
            
            $character_rank_points = 0;
            
            $character_score = $data_row["{$name}_score"];
            $character_score_rank = $data_row["{$name}_score_rank"];
            $character_score_rank_points = NULL;
            
            if(!empty($character_score_rank)) {
                $character_score_rank_points = static::generateRankPoints($character_score_rank);
                
                $character_rank_points += $character_score_rank_points;
                
                if(!isset($total_score_points)) {
                    $total_score_points = 0;
                }
                
                $total_score_points += $character_score_rank_points;
                
                if(!isset($score_total)) {
                    $score_total = 0;
                }
                
                $score_total += $character_score;
            }
            
            $character_score_rankings = array(
                'rank' => $character_score_rank,
                'rank_points' => $character_score_rank_points,
                'score' => $character_score,
            );
            
            $character_speed_time = $data_row["{$name}_speed_time"];
            $character_speed_rank = $data_row["{$name}_speed_rank"];
            $character_speed_rank_points = NULL;
            
            if(!empty($character_speed_rank)) {
                $character_speed_rank_points = static::generateRankPoints($character_speed_rank);
                
                $character_rank_points += $character_speed_rank_points;
                
                if(!isset($total_speed_points)) {
                    $total_speed_points = 0;
                }
                
                $total_speed_points += $character_speed_rank_points;
                
                if(!isset($speed_total_time)) {
                    $speed_total_time = 0;
                }
                
                $speed_total_time += $character_speed_time;
            }
            
            $character_speed_rankings = array(
                'rank' => $character_speed_rank,
                'rank_points' => $character_speed_rank_points,
                'time' => $character_speed_time
            );
            
            $character_deathless_rankings = array();
            
            if(array_key_exists("{$name}_deathless_rank", $data_row)) {
                $character_deathless_win_count = $data_row["{$name}_deathless_win_count"];
                $character_deathless_rank = $data_row["{$name}_deathless_rank"];
                $character_deathless_rank_points = NULL;
            
                if(!empty($character_deathless_rank)) {
                    $character_deathless_rank_points = static::generateRankPoints($character_deathless_rank);
                    
                    $character_rank_points += $character_deathless_rank_points;
                    
                    if(!isset($total_deathless_points)) {
                        $total_deathless_points = 0;
                    }
                    
                    $total_deathless_points += $character_deathless_rank_points;
                    
                    if(!isset($deathless_total_win_count)) {
                        $deathless_total_win_count = 0;
                    }
                    
                    $deathless_total_win_count += $character_deathless_win_count;
                }
            
                $character_deathless_rankings = array(
                    'rank' =>  $character_deathless_rank,
                    'rank_points' => $character_deathless_rank_points,
                    'win_count' => $character_deathless_win_count,
                );
            }
            
            $total_points += $character_rank_points;
            
            $processed_row[$name] = array(
                'score' => $character_score_rankings,
                'speed' => $character_speed_rankings,
                'deathless' => $character_deathless_rankings,
                'rank' => $data_row["{$name}_rank"],
                'rank_points' => $character_rank_points
            );
        }

        $score_rankings = array(
            'total_score' => $score_total,
            'rank' => $data_row['score_rank'],
            'rank_points' => $total_score_points
        );
        
        $processed_row['score'] = $score_rankings;
        
        $speed_rankings = array(
            'total_time' => $speed_total_time,
            'rank' => $data_row['speed_rank'],
            'rank_points' => $total_speed_points
        );
        
        $processed_row['speed'] = $speed_rankings;
        
        $deathless_rankings = array(
            'total_win_count' => $deathless_total_win_count,
            'rank' => $data_row['deathless_rank'],
            'rank_points' => $total_deathless_points
        );
        
        $processed_row['deathless'] = $deathless_rankings;
        
        $processed_row['rank'] = $data_row['rank'];
        $processed_row['total_points'] = $total_points;
        
        return $processed_row;
    }
}