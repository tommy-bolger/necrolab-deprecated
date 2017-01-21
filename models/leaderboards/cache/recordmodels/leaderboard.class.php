<?php

namespace Modules\Necrolab\Models\Leaderboards\Cache\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class Leaderboard
extends RecordModel {  
    protected $lbid;
    
    protected $name;
    
    protected $character_name;
    
    protected $is_speedrun;
    
    protected $is_custom;
    
    protected $is_co_op;
    
    protected $is_seeded;
    
    protected $is_daily;
    
    protected $daily_date;
    
    protected $is_score_run;
    
    protected $is_all_character;
    
    protected $is_deathless;
    
    protected $is_story_mode;
    
    protected $is_dev;
    
    protected $is_prod;
    
    protected $is_power_ranking;
    
    protected $is_daily_ranking; 
    
    protected function getPropertyValue($property_name, $property_value) { 
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'lbid':
            case 'is_speedrun':
            case 'is_custom':
            case 'is_co_op':
            case 'is_seeded':
            case 'is_daily':
            case 'is_score_run':
            case 'is_all_character':
            case 'is_deathless':
            case 'is_story_mode':
            case 'is_dev':
            case 'is_prod':
            case 'is_power_ranking':
            case 'is_daily_ranking':
                $new_property_value = (int)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
}