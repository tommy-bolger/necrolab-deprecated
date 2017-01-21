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
}