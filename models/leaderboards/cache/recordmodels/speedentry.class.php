<?php

namespace Modules\Necrolab\Models\Leaderboards\Cache\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class SpeedEntry
extends RecordModel {  
    protected $steamid;
    
    protected $personaname = 'N/A';
    
    protected $rank;
    
    protected $ugcid;
    
    protected $time;
    
    protected $is_win;
    
    protected $zone;
    
    protected $level;
    
    protected function getPropertyValue($property_name, $property_value) { 
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'steamid':
            case 'rank':
            case 'ugcid':
            case 'is_win':
            case 'zone':
            case 'level':
                $new_property_value = (int)$property_value;
                break;
            case 'time':
                $new_property_value = (float)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
} 
