<?php
namespace Modules\Necrolab\Controllers\Page\Players\Player\Login;

use \Depotwarehouse\OAuth2\Client\Twitch\Provider\Twitch as TwitchOauthProvider;
use \Framework\Utilities\Encryption;
use \Modules\Necrolab\Models\Users\Twitch\Database\Twitch as DatabaseTwitchUsers;
use \Modules\Necrolab\Models\Users\Twitch\Database\UserTokens as DatabaseUserTokens;
use \Modules\Necrolab\Models\Users\Twitch\Database\RecordModels\TwitchUser as DatabaseTwitchUser;
use \Modules\Necrolab\Models\Users\Twitch\Database\RecordModels\TwitchUsertoken as DatabaseTwitchUserToken;

class Twitch
extends OAuth2 {        
    protected $oauth_name = 'twitch';
    
    protected function getProvider() {            
        $provider = new TwitchOauthProvider(array(
            'clientId' => Encryption::decrypt($this->module->configuration->twitch_client_id),
            'clientSecret' => Encryption::decrypt($this->module->configuration->twitch_client_secret),
            'redirectUri' => $this->getRedirectUri(),
            'scopes' => array(
                'user_read',
                'channel_subscriptions', 
                'user_follows_edit'
            )
        ));
        
        return $provider;
    }
    
    protected function saveUserData($user) {
        $twitch_user_record = new DatabaseTwitchUser();
        
        $twitch_user_record->setPropertiesFromOAuthData($user->toArray());
        
        $twitch_user_id = DatabaseTwitchUsers::save($twitch_user_record);
        
        return $twitch_user_id;
    }
    
    protected function saveUserToken($twitch_user_id, $token) {
        $twitch_user_token_record = new DatabaseTwitchUserToken();
        
        $twitch_user_token_record->setPropertiesFromOAuthToken($twitch_user_id, $token);
        
        DatabaseUserTokens::save($twitch_user_id, $twitch_user_token_record);
    }
}