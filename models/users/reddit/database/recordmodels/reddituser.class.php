<?php

namespace Modules\Necrolab\Models\Users\Reddit\Database\RecordModels;

use \Exception;
use \DateTime;
use \Framework\Core\RecordModel;

class RedditUser
extends RecordModel {    
    protected $reddit_id;
    
    protected $username;
    
    protected $comment_karma;
    
    protected $link_karma;
    
    protected $over_18;
    
    protected $has_gold;
    
    protected $is_employee;
    
    protected $reddit_created;
    
    protected $updated;
    
    public function setPropertiesFromOAuthData(array $user_data) {
        if(!empty($user_data)) {
            $reddit_created = new DateTime("@{$user_data['created_utc']}");
                        
            $this->reddit_id = $user_data['id'];
            $this->username = $user_data['name'];
            $this->comment_karma = (int)$user_data['comment_karma'];
            $this->link_karma = (int)$user_data['link_karma'];
            $this->over_18 = (int)$user_data['over_18'];
            $this->has_gold = (int)$user_data['is_gold'];
            $this->is_employee = (int)$user_data['is_employee'];
            $this->reddit_created = $reddit_created->format('Y-m-d H:i:s');
        }
    }
}