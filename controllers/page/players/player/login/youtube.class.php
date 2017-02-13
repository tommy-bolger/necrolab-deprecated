<?php
namespace Modules\Necrolab\Controllers\Page\Players\Player\Login;

use \Framework\Core\Loader;
use \League\OAuth2\Client\Provider\YouTube as YoutubeOauthProvider;
use \Modules\Necrolab\Models\Users\Youtube\Database\Youtube as DatabaseYoutubeUsers;
use \Modules\Necrolab\Models\Users\Youtube\Database\UserTokens as DatabaseUserTokens;
use \Modules\Necrolab\Models\Users\Youtube\Database\RecordModels\YoutubeUser as DatabaseYoutubeUser;
use \Modules\Necrolab\Models\Users\Youtube\Database\RecordModels\YoutubeUsertoken as DatabaseYoutubeUserToken;

class Youtube
extends OAuth2 {        
    protected $oauth_name = 'youtube';
    
    public function init() {
        parent::init();
        
        Loader::load('oauth2-youtube-master/src/Provider/YouTube.php');
        Loader::load('oauth2-youtube-master/src/Provider/YouTubeResourceOwner.php');
    }
    
    protected function getProvider() {            
        $provider = new YoutubeOauthProvider(array(
            'clientId' => $this->module->configuration->youtube_client_id,
            'clientSecret' => $this->module->configuration->youtube_client_secret,
            'redirectUri' => $this->getRedirectUri()
        ));
        
        return $provider;
    }
    
    protected function saveUserData($user) {   
        $youtube_user_record = new DatabaseYoutubeUser();
        
        $youtube_user_record->setPropertiesFromOAuthData($user->toArray());
        
        $youtube_user_id = DatabaseYoutubeUsers::save($youtube_user_record);
        
        return $youtube_user_id;
    }
    
    protected function saveUserToken($youtube_user_id, $token) {
        $youtube_user_token_record = new DatabaseYoutubeUserToken();
        
        $youtube_user_token_record->setPropertiesFromOAuthToken($youtube_user_id, $token);
        
        DatabaseUserTokens::save($youtube_user_id, $youtube_user_token_record);
    }
}