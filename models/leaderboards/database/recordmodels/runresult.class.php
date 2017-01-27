<?php

namespace Modules\Necrolab\Models\Leaderboards\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class RunResult
extends RecordModel {
    protected $name;
    
    protected $is_win = 0;
}