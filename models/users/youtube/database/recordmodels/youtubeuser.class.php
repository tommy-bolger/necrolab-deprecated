<?php

namespace Modules\Necrolab\Models\Users\Youtube\Database\RecordModels;

use \Exception;
use \DateTime;
use \Framework\Core\RecordModel;

class YoutubeUser
extends RecordModel {    
    protected $youtube_id;
    
    protected $etag;
    
    protected $title;
    
    protected $description;
    
    protected $default_thumbnail;
    
    protected $medium_thumbnail;
    
    protected $high_thumbnail;
    
    protected $updated;
    
    public function setPropertiesFromOAuthData(array $user_data) {
        if(!empty($user_data)) {              
            $first_channel = current($user_data['items']);
        
            $this->youtube_id = $first_channel['id'];
            $this->etag = $first_channel['etag'];
            
            
            $snippet = array();
            
            if(!empty($first_channel['snippet'])) {
                $snippet = $first_channel['snippet'];
            }
            
            if(!empty($snippet['title'])) {
                $this->title = $snippet['title'];
            }
            
            if(!empty($snippet['description'])) {
                $this->description = $snippet['description'];
            }
            
            if(!empty($snippet['thumbnails'])) {
                $this->default_thumbnail = $snippet['thumbnails']['default']['url'];
                $this->medium_thumbnail = $snippet['thumbnails']['medium']['url'];
                $this->high_thumbnail = $snippet['thumbnails']['high']['url'];
            }
        }
    }
}