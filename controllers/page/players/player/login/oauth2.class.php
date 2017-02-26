<?php
namespace Modules\Necrolab\Controllers\Page\Players\Player\Login;

use \Framework\Core\Loader;
use \Framework\Utilities\Http;
use \League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as DatabaseSteamUsers;
use \Modules\Necrolab\Controllers\Page\Players\Player\Player;

class OAuth2
extends Player {        
    protected $oauth_name;
    
    protected $steamid;

    public function init() {
        if(!isset(session()->steamid)) {
            Http::redirect('/players/player/login/logout');
        }
        
        $this->steamid = session()->steamid;
        
        Loader::load('autoload.php', true, false);
    }
    
    public function setup() {}
    
    protected function getRedirectUri() {
        $redirect_uri = '';
        
        if($this->framework->configuration->environment == 'production') {
            $redirect_uri = "https://api.necrolab.com/players/player/login/{$this->oauth_name}";
        }
        else {
            $redirect_uri = "http://dev.necrolab.com/players/player/login/{$this->oauth_name}";
        }
    
        return ;
    }
    
    protected function getProvider() {
        return false;
    }
    
    protected function getAuthorizationUrl($provider) {
        return $provider->getAuthorizationUrl(array(
            'duration' => 'permanent'
        ));
    }
    
    protected function saveUserData($user) {
        return false;
    }
    
    protected function linkToSteamUser($user_id, $user_id_name) {
        $steam_user = DatabaseSteamUsers::getRecordModel(array(
            'steamid' => $this->steamid,
            $user_id_name => $user_id
        ));
        
        DatabaseSteamUsers::save($steam_user);
    }
    
    protected function saveUserToken($user_id, $token) {}
    
    public function actionGet() {
        $state_name = "{$this->oauth_name}_oauth2_state";
    
        $user_id_name = "{$this->oauth_name}_user_id";
        
        if(!isset(session()->$user_id_name)) {
            $provider = $this->getProvider();

            $code = request()->get->code;
            $state = request()->get->state;

            // If we don't have an authorization code then get one
            if(empty($code)) {  
                // Fetch the authorization URL from the provider; this returns the
                // urlAuthorize option and generates and applies any necessary parameters
                // (e.g. state).
                $authorization_url = $this->getAuthorizationUrl($provider);

                // Get the state generated for you and store it to the session.
                session()->$state_name = $provider->getState();

                // Redirect the user to the authorization URL.
                Http::redirect($authorization_url);
            } 
            // Check given state against previously stored one to mitigate CSRF attack
            elseif(empty($state) || ($state !== session()->$state_name)) {
                session()->end();
                
                Http::redirect('/players/player/login/logout');
            } 
            else {
                $successful_retrieval = true;
            
                try {
                    $token = $provider->getAccessToken("authorization_code", array(
                        'code' => $code
                    ));

                    $user = $provider->getResourceOwner($token);
                }
                catch(IdentityProviderException $exception) {
                    $successful_retrieval = false;
                }
                    
                if($successful_retrieval) {
                    $user_id = $this->saveUserData($user);
                    
                    $this->linkToSteamUser($user_id, $user_id_name);
                    
                    $this->saveUserToken($user_id, $token);
                    
                    session()->$user_id_name = $user_id;
                    
                    unset(session()->$state_name);
                }
            }
        }
        
        Http::redirect(Http::generateUrl('/players/player', array(
            'id' => $this->steamid
        )));
    }
    
    public function actionPost() {}
}