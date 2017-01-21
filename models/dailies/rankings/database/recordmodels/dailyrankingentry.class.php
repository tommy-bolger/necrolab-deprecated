<?php

namespace Modules\Necrolab\Models\Dailies\Rankings\Database\RecordModels;

use \Framework\Core\RecordModel;

class DailyRankingEntry
extends RecordModel {     
    protected $daily_ranking_id;
  
    protected $steam_user_id;

    protected $first_place_ranks = 0;
    
    protected $top_5_ranks = 0;
    
    protected $top_10_ranks = 0;
    
    protected $top_20_ranks = 0;
    
    protected $top_50_ranks = 0;
    
    protected $top_100_ranks = 0;
    
    protected $total_points;
    
    protected $points_per_day;
    
    protected $total_dailies;
    
    protected $total_wins = 0;
    
    protected $average_rank;
    
    protected $sum_of_ranks;
    
    protected $rank;
}