<?php

namespace Modules\Necrolab\Models\Rankings\Cache\Speed\RecordModels;

use \Framework\Core\RecordModel;

class SpeedEntry
extends RecordModel {    
    protected $steamid;

    protected $cadence_speed_rank;

    protected $cadence_speed_rank_points;

    protected $cadence_speed_time;

    protected $bard_speed_rank;

    protected $bard_speed_rank_points;

    protected $bard_speed_time;

    protected $monk_speed_rank;

    protected $monk_speed_rank_points;

    protected $monk_speed_time;

    protected $aria_speed_rank;

    protected $aria_speed_rank_points;

    protected $aria_speed_time;

    protected $bolt_speed_rank;

    protected $bolt_speed_rank_points;

    protected $bolt_speed_time;

    protected $dove_speed_rank;

    protected $dove_speed_rank_points;

    protected $dove_speed_time;

    protected $eli_speed_rank;

    protected $eli_speed_rank_points;

    protected $eli_speed_time;

    protected $melody_speed_rank;

    protected $melody_speed_rank_points;

    protected $melody_speed_time;

    protected $dorian_speed_rank;

    protected $dorian_speed_rank_points;

    protected $dorian_speed_time;

    protected $coda_speed_rank;

    protected $coda_speed_rank_points;

    protected $coda_speed_time;

    protected $all_speed_rank;

    protected $all_speed_rank_points;

    protected $all_speed_time;

    protected $story_speed_rank;

    protected $story_speed_rank_points;

    protected $story_speed_time;

    protected $speed_rank_points_total;

    protected $speed_rank;
    
    protected function getPropertyValue($property_name, $property_value) { 
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'steamid':
            case 'cadence_speed_rank':
            case 'cadence_speed_time':
            case 'bard_speed_rank':
            case 'bard_speed_time':
            case 'monk_speed_rank':
            case 'monk_speed_time':
            case 'aria_speed_rank':
            case 'aria_speed_time':
            case 'bolt_speed_rank':
            case 'bolt_speed_time':
            case 'dove_speed_rank':
            case 'dove_speed_time':
            case 'eli_speed_rank':
            case 'eli_speed_time':
            case 'melody_speed_rank':
            case 'melody_speed_time':
            case 'dorian_speed_rank':
            case 'dorian_speed_time':
            case 'coda_speed_rank':
            case 'coda_speed_time':
            case 'all_speed_rank':
            case 'all_speed_time':
            case 'story_speed_rank':
            case 'story_speed_time':
            case 'speed_rank':
                $new_property_value = (int)$property_value;
                break;
            case 'cadence_speed_rank_points':
            case 'bard_speed_rank_points':
            case 'monk_speed_rank_points':
            case 'aria_speed_rank_points':
            case 'bolt_speed_rank_points':
            case 'dove_speed_rank_points':
            case 'eli_speed_rank_points':
            case 'melody_speed_rank_points':
            case 'dorian_speed_rank_points':
            case 'coda_speed_rank_points':
            case 'all_speed_rank_points':
            case 'story_speed_rank_points':
            case 'speed_rank_points_total':
                $new_property_value = (float)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
}