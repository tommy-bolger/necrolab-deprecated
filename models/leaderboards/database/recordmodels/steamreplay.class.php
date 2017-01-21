<?php

namespace Modules\Necrolab\Models\Leaderboards\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class SteamReplay
extends RecordModel {
    protected $steam_user_id;
    
    protected $ugcid;
    
    protected $seed;
    
    protected $downloaded;
    
    protected $invalid;
}