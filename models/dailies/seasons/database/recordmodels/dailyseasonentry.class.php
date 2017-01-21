<?php
namespace Modules\Necrolab\Models\DailySeasons\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class DailyRankingEntry
extends RecordModel {     
    protected $daily_ranking_season_id;
    
    protected $daily_ranking_season_snapshot_id;
  
    protected $steam_user_id;

    protected $first_place_ranks;
    
    protected $top_5_ranks;
    
    protected $top_10_ranks;
    
    protected $top_20_ranks;
    
    protected $top_50_ranks;
    
    protected $top_100_ranks;
    
    protected $total_points;
    
    protected $points_per_day;
    
    protected $total_dailies;
    
    protected $total_wins;
    
    protected $average_rank;
    
    protected $sum_of_ranks;
    
    protected $rank;
}