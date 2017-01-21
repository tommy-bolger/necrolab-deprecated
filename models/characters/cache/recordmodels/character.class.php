<?php

namespace Modules\Necrolab\Models\Characters\Cache\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class Character
extends RecordModel {    
    protected $name;
    
    protected $display_name;
    
    protected $is_active;
    
    protected $sort_order;
    
    protected function getPropertyValue($property_name, $property_value) { 
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'is_active':
            case 'sort_order':
                $new_property_value = (int)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
}