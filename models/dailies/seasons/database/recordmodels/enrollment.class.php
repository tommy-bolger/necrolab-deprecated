<?php

namespace Modules\Necrolab\Models\DailySeasons\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class Enrollment
extends RecordModel {    
    protected $daily_ranking_season_id;
    
    protected $steam_user_id;
    
    protected $enrolled;
    
    protected $unenrolled;
}