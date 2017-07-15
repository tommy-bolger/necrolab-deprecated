<?php

namespace Modules\Necrolab\Models\SteamUsers\Database\RecordModels;

use \DateTime;
use \Framework\Core\RecordModel;
use \Modules\Necrolab\Models\Leaderboards\Database\Entry;
use \Modules\Necrolab\Models\Leaderboards\Database\Details;

class SteamUserPb
extends RecordModel {
    const MAX_VALID_SCORE = 1000000;

    protected $leaderboard_id;
  
    protected $steam_user_id;
    
    protected $score;
    
    protected $first_leaderboard_snapshot_id;
    
    protected $first_rank;
    
    protected $time;
    
    protected $win_count;
    
    protected $zone;
    
    protected $level;
    
    protected $is_win = 0;
    
    protected $leaderboard_entry_details_id;
    
    protected $steam_replay_id;
    
    public function setPropertiesFromSteamObject($steam_object, $leaderboard, $rank, DateTime $date) {    
        $this->score = (int)$steam_object->score;
        $this->first_rank = $rank;
        
        $this->leaderboard_entry_details_id = Details::save($steam_object->details);

        if($leaderboard->is_speedrun == 1) {            
            $this->time = (float)Entry::getTime($this->score);
        }
        
        //This logic path is for importing XML.
        if(!empty($steam_object->details)) {
            $highest_zone_level = Entry::getHighestZoneLevel($steam_object->details);
        
            if(!empty($highest_zone_level)) {
                $this->zone = (int)$highest_zone_level['highest_zone'];
                $this->level = (int)$highest_zone_level['highest_level'];
            }
        }
        //And this logic path is for importing from Marukyu's importer.
        else {
            $this->zone = (int)$steam_object->zone;
            $this->level = (int)$steam_object->level;
        }

        if(empty($leaderboard->is_speedrun)) {
            $this->is_win = Entry::getIfWin($date, $leaderboard->release_id, $this->zone, $this->level);
        }
        else {        
            $this->is_win = 1;
        }
        
        $win_count = NULL;
        
        if($leaderboard->is_deathless == 1) {
            $this->win_count = Entry::getWinCount($this->score);
        }
    }
    
    public function isValid($leaderboard_record) {
        return (($leaderboard_record->is_score_run == 1 && $this->score <= self::MAX_VALID_SCORE) || $leaderboard_record->is_speedrun == 1);
    }
}