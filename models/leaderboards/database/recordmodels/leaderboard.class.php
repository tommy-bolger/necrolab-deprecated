<?php

namespace Modules\Necrolab\Models\Leaderboards\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class Leaderboard
extends RecordModel {  
    protected $name;
    
    protected $url;
    
    protected $lbid;
    
    protected $display_name;
    
    protected $entries;
    
    protected $sortmethod;
    
    protected $displaytype;
    
    protected $onlytrustedwrites;
    
    protected $onlyfriendsreads;
    
    protected $character_id;
    
    protected $is_speedrun;
    
    protected $is_custom;
    
    protected $is_co_op;
    
    protected $is_seeded;
    
    protected $is_daily;
    
    protected $daily_date;
    
    protected $is_score_run;
    
    protected $is_all_character;
    
    protected $is_deathless;
    
    protected $is_story_mode;
    
    protected $last_snapshot_id;
    
    protected $is_dev;
    
    protected $is_prod;
    
    protected $is_power_ranking;
    
    protected $is_daily_ranking; 
    
    protected $is_dlc;
    
    protected $release_id;
}