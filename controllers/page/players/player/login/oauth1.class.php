<?php
namespace Modules\Necrolab\Controllers\Page\Players\Player\Login;

use \Exception;
use \Framework\Utilities\Http;

class OAuth1
extends OAuth2 {    
    public function actionGet() {
        $temporary_credentials_name = "{$this->oauth_name}_temporary_credentials";
    
        $user_id_name = "{$this->oauth_name}_user_id";
        
        if(!isset(session()->$user_id_name)) {
            $provider = $this->getProvider();

            $oauth_token = request()->get->oauth_token;
            $oauth_verifier = request()->get->oauth_verifier;
            $error = request()->error;
            $denied = request()->denied;

            // If we don't have an authorization then get one
            if(empty($oauth_token) && empty($oauth_verifier)) { 
                // Retrieve temporary credentials
                $temporary_credentials = $provider->getTemporaryCredentials();

                // Store credentials in the session for use later
                session()->$temporary_credentials_name = serialize($temporary_credentials);

                // Second part of OAuth 1.0 authentication is to redirect the
                // resource owner to the login screen on the server.
                $provider->authorize($temporary_credentials);
            
                exit;
            }
            elseif(empty($error) && empty($denied)) {
                if(isset(session()->$temporary_credentials_name)) {
                    $successful_retrieval = true;
                    
                    $token_credentials = NULL;
                
                    try {
                        // Retrieve the temporary credentials we saved before
                        $temporary_credentials = unserialize(session()->$temporary_credentials_name);

                        // We will now retrieve token credentials from the server
                        $token_credentials = $provider->getTokenCredentials($temporary_credentials, $oauth_token, $oauth_verifier);
                    }
                    catch(Exception $exception) {
                        $successful_retrieval = false;
                    }
                    
                    if($successful_retrieval) {
                        // Save user details
                        $user_id = $this->saveUserData($provider->getUserDetails($token_credentials));
                        
                        $this->linkToSteamUser($user_id, $user_id_name);
                        
                        $this->saveUserToken($user_id, $token_credentials);
                        
                        unset(session()->$temporary_credentials_name);
                        
                        session()->$user_id_name = $user_id;
                    }
                }
            }
        }
        
        Http::redirect(Http::generateUrl('/players/player', array(
            'id' => $this->steamid
        )));
    }
}