<?php

namespace Modules\Necrolab\Models\Characters\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class Character
extends RecordModel {
    protected $character_id;
    
    protected $name;
    
    protected $display_name;
    
    protected $is_active;
    
    protected $is_weighted;
    
    protected $sort_order;
}