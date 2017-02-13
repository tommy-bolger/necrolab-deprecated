<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \Modules\Necrolab\Models\Necrolab;

class Entry
extends Necrolab {
    public static function getHighestZoneLevel($details) {
        $details_split = explode('0000000', $details);
                            
        $highest_zone = (int)$details_split[0];
        $highest_level = (int)str_replace('0', '', $details_split[1]);
        
        return array(
            'highest_zone' => $highest_zone,
            'highest_level' => $highest_level
        );
    }
    
    public static function getIfWin($details) {
        $is_win = 'No';
                            
        if($details == "0400000006000000") {
            $is_win = "Yes";
        }
    }
    
    public static function getWinCount($score) {
        $win_count = NULL;
    
        if(!empty($score)) {
            $win_count = $score / 100;
            $win_count = round($win_count);
        }
        
        return $win_count;
    }
    
    public static function getTime($score) {
        $time = NULL;
    
        if(!empty($score)) {
            $time = (100000000 - $score) / 1000;
        }
        
        return $time;
    }
    
    public static function getFormattedApiRecord(array $data_row) {
        $processed_row = array();
        
        $processed_row['rank'] = $data_row['rank'];
        $processed_row['seed'] = $data_row['seed'];
        $processed_row['ugcid'] = $data_row['ugcid'];
        $processed_row['details'] = $data_row['details'];
        $processed_row['zone'] = $data_row['zone'];
        $processed_row['level'] = $data_row['level'];
        $processed_row['win'] = $data_row['is_win'];
        $processed_row['score'] = $data_row['score'];
        
        if(!empty($data_row['is_deathless'])) {
            $processed_row['win_count'] = $data_row['win_count'];
        }
        else {
            if(!empty($data_row['is_speedrun'])) {
                $processed_row['time'] = $data_row['time'];
            }
        }
        
        return $processed_row;
    }
}