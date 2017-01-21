<?php

namespace Modules\Necrolab\Models\Import\Steam\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class LeaderboardEntry
extends RecordModel {    
    protected $steamid;
    
    protected $score;
    
    protected $rank;
    
    protected $ugcid;
    
    protected $details;
    
    protected $zone;
    
    protected $level;
    
    protected $time;
    
    protected $is_win;
    
    protected $win_count;
    
    public function setPropertiesFromArray() {}
    
    public function setPropertiesFromObject() {}
    
    public function setPropertiesFromSteamObject($steam_object, $leaderboard) {
        
    }
}