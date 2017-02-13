<?php

namespace Modules\Necrolab\Models\Users\Twitter\Database\RecordModels;

use \Exception;
use \DateTime;
use \Framework\Core\RecordModel;

class TwitterUser
extends RecordModel {    
    protected $twitter_id;
    
    protected $nickname;
    
    protected $name;
    
    protected $email;
    
    protected $description;
    
    protected $avatar;
    
    protected $followers_count;
    
    protected $friends_count;
    
    protected $statuses_count;
    
    protected $verified;
    
    protected $updated;
    
    public function setPropertiesFromOAuthData($user_data) {
        if(!empty($user_data)) {
            $this->twitter_id = $user_data->uid;
            $this->nickname = $user_data->nickname;
            $this->name = $user_data->name;
            $this->email = $user_data->email;
            $this->description = $user_data->description;
            $this->avatar = $user_data->imageUrl;
            
            if(isset($user_data->extra)) {            
                if(isset($user_data->extra['followers_count'])) {
                    $this->followers_count = $user_data->extra['followers_count'];
                }
                
                if(isset($user_data->extra['friends_count'])) {
                    $this->friends_count = $user_data->extra['friends_count'];
                }
                
                if(isset($user_data->extra['statuses_count'])) {
                    $this->statuses_count = $user_data->extra['statuses_count'];
                }
                
                if(isset($user_data->extra['verified'])) {
                    $this->verified = (int)$user_data->extra['verified'];
                }
            }
        }
    }
}