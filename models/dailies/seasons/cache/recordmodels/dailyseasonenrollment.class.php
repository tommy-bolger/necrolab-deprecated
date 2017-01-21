<?php

namespace Modules\Necrolab\Models\DailySeasons\Cache\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class DailySeasonEnrollment
extends RecordModel {        
    protected $steamid;
    
    protected $enrolled;
    
    protected $unenrolled;
    
    protected function getPropertyValue($property_name, $property_value) { 
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'steamid':
                $new_property_value = (int)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
}