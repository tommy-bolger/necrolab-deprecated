<?php
namespace Modules\Necrolab\Controllers\Page\Players\Player\Login;

use \League\OAuth2\Client\Provider\GenericProvider as BeamproOauthProvider;
use \Framework\Utilities\Encryption;
use \Modules\Necrolab\Models\Users\Beampro\Database\Beampro as DatabaseBeamproUsers;
use \Modules\Necrolab\Models\Users\Beampro\Database\UserTokens as DatabaseUserTokens;
use \Modules\Necrolab\Models\Users\Beampro\Database\RecordModels\BeamproUser as DatabaseBeamproUser;
use \Modules\Necrolab\Models\Users\Beampro\Database\RecordModels\BeamproUsertoken as DatabaseBeamproUserToken;

class Beampro
extends OAuth2 {        
    protected $oauth_name = 'beampro';
    
    protected function getProvider() {            
        $provider = new BeamproOauthProvider(array(
            'clientId' => Encryption::decrypt($this->module->configuration->beampro_client_id),
            'clientSecret' => Encryption::decrypt($this->module->configuration->beampro_client_secret),
            'redirectUri' => $this->getRedirectUri(),
            'urlAuthorize' => 'https://beam.pro/oauth/authorize',
            'urlAccessToken' => 'https://beam.pro/api/v1/oauth/token',
            'urlResourceOwnerDetails' => 'https://beam.pro/api/v1/users/current',
            'scopes' => array(
                'channel:details:self'
            )
        ));
        
        return $provider;
    }
    
    protected function saveUserData($user) {
        $beampro_user_record = new DatabaseBeamproUser();
        
        $beampro_user_record->setPropertiesFromOAuthData($user->toArray());
        
        $beampro_user_id = DatabaseBeamproUsers::save($beampro_user_record);
        
        return $beampro_user_id;
    }
    
    protected function saveUserToken($beampro_user_id, $token) {
        $beampro_user_token_record = new DatabaseBeamproUserToken();
        
        $beampro_user_token_record->setPropertiesFromOAuthToken($beampro_user_id, $token);
        
        DatabaseUserTokens::save($beampro_user_id, $beampro_user_token_record);
    }
}