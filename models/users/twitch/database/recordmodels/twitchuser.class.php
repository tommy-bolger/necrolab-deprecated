<?php

namespace Modules\Necrolab\Models\Users\Twitch\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class TwitchUser
extends RecordModel {    
    protected $twitch_id;
    
    protected $email;
    
    protected $partnered;
    
    protected $logo;
    
    protected $bio;
    
    protected $username;
    
    protected $user_display_name;
    
    protected $updated;
    
    public function setPropertiesFromOAuthData(array $user_data) {
        if(!empty($user_data)) {                        
            $this->twitch_id = $user_data['id'];
            $this->username = $user_data['username'];
            $this->user_display_name = $user_data['display_name'];
            $this->logo = $user_data['logo'];
            $this->bio = $user_data['bio'];
            $this->partnered = (int)$user_data['partnered'];
            $this->email = $user_data['email'];
        }
    }
}