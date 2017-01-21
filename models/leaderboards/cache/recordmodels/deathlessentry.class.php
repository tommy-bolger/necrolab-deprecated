<?php

namespace Modules\Necrolab\Models\Leaderboards\Cache\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class DeathlessEntry
extends RecordModel {  
    protected $steamid;
    
    protected $personaname = 'N/A';
    
    protected $score;
    
    protected $rank;
    
    protected $ugcid;
    
    protected $is_win;
    
    protected $zone;
    
    protected $level;
    
    protected $win_count;
    
    protected function getPropertyValue($property_name, $property_value) { 
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'steamid':
            case 'score':
            case 'rank':
            case 'ugcid':
            case 'is_win':
            case 'zone':
            case 'level':
            case 'win_count':
                $new_property_value = (int)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
} 
