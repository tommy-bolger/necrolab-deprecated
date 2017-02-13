<?php

namespace Modules\Necrolab\Models\Users\Discord\Database\RecordModels;

use \Exception;
use \DateTime;
use \Framework\Core\RecordModel;

class DiscordUser
extends RecordModel {    
    protected $discord_id;
    
    protected $username;
    
    protected $email;
    
    protected $discriminator;
    
    protected $avatar;
    
    protected $updated;

    public function setPropertiesFromOAuthData(array $user_data) {
        if(!empty($user_data)) {                        
            $this->discord_id = $user_data['id'];
            $this->username = $user_data['username'];
            $this->email = $user_data['email'];
            $this->discriminator = $user_data['discriminator'];
            $this->avatar = $user_data['avatar'];
        }
    }
}