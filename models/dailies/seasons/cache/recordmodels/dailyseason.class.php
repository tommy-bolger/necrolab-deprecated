<?php
namespace Modules\Necrolab\Models\DailySeasons\Cache\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class DailySeason
extends RecordModel {        
    protected $season_number;
    
    protected $start_date;
    
    protected $end_date;
    
    protected $enrollment_start_date;
    
    protected $enrollment_end_date;
    
    protected $prize_payment_date;
    
    protected $is_latest;
    
    protected function getPropertyValue($property_name, $property_value) { 
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'season_number':
            case 'is_latest':
                $new_property_value = (int)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
}