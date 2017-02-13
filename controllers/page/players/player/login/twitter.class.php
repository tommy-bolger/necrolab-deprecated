<?php
namespace Modules\Necrolab\Controllers\Page\Players\Player\Login;

use \Framework\Core\Loader;
use \League\OAuth1\Client\Server\Twitter as TwitterOauthProvider;
use \Modules\Necrolab\Models\Users\Twitter\Database\Twitter as DatabaseTwitterUsers;
use \Modules\Necrolab\Models\Users\Twitter\Database\UserTokens as DatabaseUserTokens;
use \Modules\Necrolab\Models\Users\Twitter\Database\RecordModels\TwitterUser as DatabaseTwitterUser;
use \Modules\Necrolab\Models\Users\Twitter\Database\RecordModels\TwitterUsertoken as DatabaseTwitterUserToken;

class Twitter
extends OAuth1 {        
    protected $oauth_name = 'twitter';
    
    protected function getProvider() {            
        $provider = new TwitterOauthProvider(array(
            'identifier' => $this->module->configuration->twitter_client_id,
            'secret' => $this->module->configuration->twitter_client_secret,
            'callback_uri' => $this->getRedirectUri(),
        ));
        
        return $provider;
    }
    
    protected function saveUserData($user) {    
        $twitter_user_record = new DatabaseTwitterUser();
        
        $twitter_user_record->setPropertiesFromOAuthData($user);
        
        $twitter_user_id = DatabaseTwitterUsers::save($twitter_user_record);
        
        return $twitter_user_id;
    }
    
    protected function saveUserToken($twitter_user_id, $token) {
        $twitter_user_token_record = new DatabaseTwitterUserToken();
        
        $twitter_user_token_record->setPropertiesFromOAuthToken($twitter_user_id, $token);
        
        DatabaseUserTokens::save($twitter_user_id, $twitter_user_token_record);
    }
}