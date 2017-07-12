<?php

namespace Modules\Necrolab\Models\Achievements;

use \Exception;
use \Framework\Core\RecordModel;

class Achievement
extends RecordModel {
    protected $name;
    
    protected $display_name;
    
    protected $description;
    
    protected $icon_url;
    
    protected $icon_gray_url;
    
    public function setPropertiesFromSteamObject($steam_object) {
        $this->name = $steam_object->name;
        $this->display_name = $steam_object->displayName;
        $this->description = $steam_object->description;
        $this->icon_url = $steam_object->icon;
        $this->icon_gray_url = $steam_object->icongray;
    }
}