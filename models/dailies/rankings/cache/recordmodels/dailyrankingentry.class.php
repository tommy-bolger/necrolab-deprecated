<?php

namespace Modules\Necrolab\Models\Dailies\Rankings\Cache\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class Entry
extends RecordModel {   
    protected $steamid;

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
    
    protected function getPropertyValue($property_name, $property_value) {
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'steamid':
            case 'first_place_ranks':
            case 'top_5_ranks':
            case 'top_10_ranks':
            case 'top_20_ranks':
            case 'top_50_ranks':
            case 'top_100_ranks':
            case 'total_dailies':
            case 'total_wins':
            case 'sum_of_ranks':
            case 'rank':
                $new_property_value = (int)$property_value;
                break;
            case 'total_points':
            case 'points_per_day':
            case 'average_rank':
                $new_property_value = (float)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
}