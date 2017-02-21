<?php

namespace Modules\Necrolab\Models\Leaderboards\Database\RecordModels;

use \DateTime;
use \Framework\Core\RecordModel;
use \Modules\Necrolab\Models\Leaderboards\Database\Entry;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers;
use \Modules\Necrolab\Models\Leaderboards\Database\Replays;
use \Modules\Necrolab\Models\Leaderboards\Database\Details;
use \Modules\Necrolab\Models\SteamUsers\RecordModels\SteamUser;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;

class LeaderboardEntry
extends RecordModel {
    const MAX_VALID_SCORE = 1000000;

    protected $leaderboard_snapshot_id;
  
    protected $steam_user_id;
    
    protected $score;
    
    protected $rank;
    
    protected $steam_replay_id;
    
    protected $leaderboard_entry_details_id;
    
    protected $time;
    
    protected $is_win = 0;
    
    protected $zone;
    
    protected $level;
    
    protected $win_count;
    
    public function setPropertiesFromSteamObject($steam_object, $leaderboard, $rank, DateTime $date) {
        $this->steam_user_id = SteamUsers::getId($steam_object->steamid);
                                    
        if(empty($this->steam_user_id)) {
            $database_steam_user = new DatabaseSteamUser();
            
            $database_steam_user->steamid = $steam_object->steamid;
        
            $this->steam_user_id = SteamUsers::save($database_steam_user, 'steam_import');
        }
    
        $this->score = (int)$steam_object->score;
        $this->rank = $rank;
        
        $this->steam_replay_id = Replays::save($steam_object->ugcid, $this->steam_user_id);
        $this->leaderboard_entry_details_id = Details::save($steam_object->details);

        if($leaderboard->is_speedrun == 1) {            
            $this->time = (float)Entry::getTime($this->score);
        }
        
        $highest_zone_level = Entry::getHighestZoneLevel($steam_object->details);
        
        if(!empty($highest_zone_level)) {
            $this->zone = (int)$highest_zone_level['highest_zone'];
            $this->level = (int)$highest_zone_level['highest_level'];
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