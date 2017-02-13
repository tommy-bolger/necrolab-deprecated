<?php
namespace Modules\Necrolab\Models\Rankings;

use \Modules\Necrolab\Models\Necrolab;

class Entry
extends Necrolab {
    public static function getFormattedApiRecord($active_characters, $data_row) {
        $processed_row = array();
    
        foreach($active_characters as $active_character) {
            $name = $active_character['name'];
            
            $character_score_rankings = array(
                'rank' => $data_row["{$name}_score_rank"],
                'rank_points' => $data_row["{$name}_score_rank_points"],
                'score' => $data_row["{$name}_score"],
            );
            
            $character_speed_rankings = array(
                'rank' => $data_row["{$name}_speed_rank"],
                'rank_points' => $data_row["{$name}_speed_rank_points"],
                'time' => $data_row["{$name}_speed_time"]
            );
            
            $character_deathless_rankings = array();
            
            if(array_key_exists("{$name}_deathless_rank", $data_row)) {
                $character_deathless_rankings = array(
                    'rank' =>  $data_row["{$name}_deathless_rank"],
                    'rank_points' => $data_row["{$name}_deathless_rank_points"],
                    'win_count' => $data_row["{$name}_deathless_win_count"],
                );
            }
            
            $processed_row[$name] = array(
                'score' => $character_score_rankings,
                'speed' => $character_speed_rankings,
                'deathless' => $character_deathless_rankings,
                'rank' => $data_row["{$name}_rank"],
                'rank_points' => $data_row["{$name}_rank_points"]
            );
        }

        $score_rankings = array(
            'total_score' => $data_row['score_total'],
            'rank' => $data_row['score_rank'],
            'rank_points' => $data_row['score_rank_points_total']
        );
        
        $processed_row['score'] = $score_rankings;
        
        $speed_rankings = array(
            'total_time' => $data_row['speed_total_time'],
            'rank' => $data_row['speed_rank'],
            'rank_points' => $data_row['speed_rank_points_total']
        );
        
        $processed_row['speed'] = $speed_rankings;
        
        $deathless_rankings = array(
            'total_win_count' => $data_row['deathless_total_win_count'],
            'rank' => $data_row['deathless_rank'],
            'rank_points' => $data_row['deathless_rank_points_total']
        );
        
        $processed_row['deathless'] = $deathless_rankings;
        
        $processed_row['rank'] = $data_row['rank'];
        $processed_row['total_points'] = $data_row['total_points'];
        
        return $processed_row;
    }
}