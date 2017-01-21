<?php

namespace Modules\Necrolab\Models\Leaderboards\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class LeaderboardEntry
extends RecordModel {      
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
}