<?php

namespace Modules\Necrolab\Models\SteamUsers\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class SteamUserAchievement
extends RecordModel {
    protected $steam_user_id;
    
    protected $achievement_id;
    
    protected $achieved;
}