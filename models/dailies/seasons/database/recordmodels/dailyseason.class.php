<?php

namespace Modules\Necrolab\Models\DailySeasons\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class DailySeason
extends RecordModel {    
    protected $daily_ranking_season_id;
    
    protected $season_number;
    
    protected $start_date;
    
    protected $end_date;
    
    protected $enrollment_start_date;
    
    protected $enrollment_end_date;
    
    protected $prize_payment_date;
    
    protected $latest_snapshot_id;
    
    protected $is_latest;
}