<?php
namespace Modules\Necrolab\Controllers\Page\Players\Player\Login;

use \Framework\Core\Loader;
use \Rudolf\OAuth2\Client\Provider\Reddit as RedditOauthProvider;
use \Modules\Necrolab\Models\Users\Reddit\Database\Reddit as DatabaseRedditUsers;
use \Modules\Necrolab\Models\Users\Reddit\Database\UserTokens as DatabaseUserTokens;
use \Modules\Necrolab\Models\Users\Reddit\Database\RecordModels\RedditUser as DatabaseRedditUser;
use \Modules\Necrolab\Models\Users\Reddit\Database\RecordModels\RedditUsertoken as DatabaseRedditUserToken;

class Reddit
extends OAuth2 { 
    protected $oauth_name = 'reddit';
    
    protected function getProvider() {
        Loader::load('oauth2-reddit-master/src/Provider/Reddit.php');
    
        $client_id = $this->module->configuration->reddit_client_id;
            
        $provider = new RedditOauthProvider([
            'clientId' => $client_id,
            'clientSecret' => $this->module->configuration->reddit_client_secret,
            'redirectUri'  => $this->getRedirectUri(),
            'userAgent' => "Necrolab:{$client_id}:5.3, (by /u/squega)",
            'scopes' => array(
                'identity', 
                'read'
            ),
        ]);
        
        return $provider;
    }
    
    protected function saveUserData($user) {    
        $reddit_user_record = new DatabaseRedditUser();
        
        $reddit_user_record->setPropertiesFromOAuthData($user->toArray());
        
        $reddit_user_id = DatabaseRedditUsers::save($reddit_user_record);
        
        return $reddit_user_id;
    }
    
    protected function saveUserToken($reddit_user_id, $token) {
        $reddit_user_token_record = new DatabaseRedditUserToken();
        
        $reddit_user_token_record->setPropertiesFromOAuthToken($reddit_user_id, $token);
        
        DatabaseUserTokens::save($reddit_user_id, $reddit_user_token_record);
    }
}