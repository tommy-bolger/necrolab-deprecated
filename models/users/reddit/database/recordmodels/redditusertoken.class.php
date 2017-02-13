<?php

namespace Modules\Necrolab\Models\Users\Reddit\Database\RecordModels;

use \Exception;
use \DateTime;
use \Framework\Core\RecordModel;

class RedditUserToken
extends RecordModel {    
    protected $reddit_user_id;
    
    protected $token;
    
    protected $refresh_token;
    
    protected $expires;
    
    protected $created;
    
    protected $expired;
    
    public function setPropertiesFromOAuthToken($reddit_user_id, $token) {    
        $this->reddit_user_id = $reddit_user_id;
        $this->token = $token->getToken();
        $this->refresh_token = $token->getRefreshToken();
        
        $expires_field = $token->getExpires();
        
        if(!empty($expires_field)) {
            $expires = new DateTime("@{$expires_field}");
        
            $this->expires = $expires->format('Y-m-d H:i:s');
        }
    }
}