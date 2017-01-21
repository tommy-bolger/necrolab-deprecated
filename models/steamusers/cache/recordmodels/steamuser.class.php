<?php

namespace Modules\Necrolab\Models\SteamUsers\Cache\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class SteamUser
extends RecordModel {    
    protected $steamid;
    
    protected $personaname;
    
    protected $twitch_username;
    
    protected $twitter_username;
    
    protected $nico_nico_url;
    
    protected $hitbox_username;
    
    protected $website;
    
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