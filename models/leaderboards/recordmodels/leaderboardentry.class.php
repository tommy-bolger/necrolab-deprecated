<?php

namespace Modules\Necrolab\Models\Leaderboards\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;
use \Modules\Necrolab\Models\Leaderboards\Entry;

class LeaderboardEntry
extends RecordModel {    
    protected $steamid;
    
    protected $rank;
    
    protected $score;
    
    protected $time;
    
    protected $win_count;
    
    protected $zone;
    
    protected $level;
    
    protected $is_win;
    
    protected $ugcid;
    
    protected $details;
    
    public function setPropertiesFromArray(array $property_values, $error_on_invalid_property = false) {}
    
    public function setPropertiesFromIndexedArray(array $indexed_property_values) {}
    
    public function setPropertiesFromObject($property_values, $error_on_invalid_property = false) {}
    
    public function setPropertiesFromSteamObject($steam_object, $leaderboard) {        
        $score = (int)$steam_object->score;
        
        $time = NULL;
        
        if($leaderboard->is_speedrun == 1) {
            $time = Entry::getTime($steam_object->score);
            
            $time = (float)$time;
        }
        
        $win_count = NULL;
        
        if($leaderboard->is_deathless == 1) {
            $win_count = Entry::getWinCount($steam_object->score);
        }
        
        $zone = NULL;
        $level = NULL;
        
        $highest_zone_level = Entry::getHighestZoneLevel($steam_object->details);
        
        if(!empty($highest_zone_level)) {
            $zone = (int)$highest_zone_level['highest_zone'];
            $level = (int)$highest_zone_level['highest_level'];
        }

        $is_win = 0;
        
        if($steam_object->details == '0400000006000000') {
            $is_win = 1;
        }
        
        $this->steamid = (int)$steam_object->steamid;
        $this->rank = (int)$steam_object->rank;
        $this->score = $score;
        $this->time = $time;
        $this->win_count = $win_count;
        $this->zone = (int)$zone;
        $this->level = (int)$level;
        $this->is_win = $is_win;
        $this->ugcid = (int)$steam_object->ugcid;
        $this->details = $steam_object->details;
    }
    
    public function isValid($leaderboard_record) {
        return (($leaderboard_record->is_score_run == 1 && $this->score <= 1000000) || $leaderboard_record->is_speedrun == 1);
    }
}