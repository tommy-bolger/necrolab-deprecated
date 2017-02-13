<?php

namespace Modules\Necrolab\Models\Users\Beampro\Database\RecordModels;

use \Exception;
use \DateTime;
use \Framework\Core\RecordModel;

class BeamproUser
extends RecordModel {    
    protected $beampro_id;
    
    protected $username;
    
    protected $avatar_url;
    
    protected $description;
    
    protected $bio;
    
    protected $channel_title;
    
    protected $views;
    
    protected $followers;
    
    protected $updated;

    public function setPropertiesFromOAuthData(array $user_data) {
        if(!empty($user_data)) {                        
            $this->beampro_id = $user_data['id'];
            $this->username = $user_data['username'];
            $this->avatar_url = $user_data['avatarUrl'];
            $this->description = $user_data['channel']['description'];
            $this->bio = $user_data['bio'];
            $this->channel_title = $user_data['channel']['name'];
            $this->views = $user_data['channel']['viewersTotal'];
            $this->followers = $user_data['channel']['numFollowers'];
        }
    }
}