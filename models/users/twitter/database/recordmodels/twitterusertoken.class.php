<?php

namespace Modules\Necrolab\Models\Users\Twitter\Database\RecordModels;

use \Exception;
use \DateTime;
use \Framework\Core\RecordModel;

class TwitterUserToken
extends RecordModel {    
    protected $twitter_user_id;
    
    protected $identifier;
    
    protected $secret;
    
    protected $expires;
    
    protected $created;
    
    protected $expired;
    
    public function setPropertiesFromOAuthToken($twitter_user_id, $token_credentials) {
        $this->twitter_user_id = $twitter_user_id;
        $this->identifier = $token_credentials->getIdentifier();
        $this->secret = $token_credentials->getSecret();
    }
}