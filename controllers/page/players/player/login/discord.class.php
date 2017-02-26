<?php
namespace Modules\Necrolab\Controllers\Page\Players\Player\Login;

use \Discord\OAuth\Discord as DiscordOauthProvider;
use \Framework\Utilities\Encryption;
use \Modules\Necrolab\Models\Users\Discord\Database\Discord as DatabaseDiscordUsers;
use \Modules\Necrolab\Models\Users\Discord\Database\UserTokens as DatabaseUserTokens;
use \Modules\Necrolab\Models\Users\Discord\Database\RecordModels\DiscordUser as DatabaseDiscordUser;
use \Modules\Necrolab\Models\Users\Discord\Database\RecordModels\DiscordUsertoken as DatabaseDiscordUserToken;

class Discord
extends OAuth2 {        
    protected $oauth_name = 'discord';
    
    protected function getProvider() {            
        $provider = new DiscordOauthProvider(array(
            'clientId' => Encryption::decrypt($this->module->configuration->discord_client_id),
            'clientSecret' => Encryption::decrypt($this->module->configuration->discord_client_secret),
            'redirectUri' => $this->getRedirectUri()
        ));
        
        return $provider;
    }
    
    protected function saveUserData($user) {
        $discord_user_record = new DatabaseDiscordUser();
        
        $discord_user_record->setPropertiesFromOAuthData($user->toArray());
        
        $discord_user_id = DatabaseDiscordUsers::save($discord_user_record);
        
        return $discord_user_id;
    }
    
    protected function saveUserToken($discord_user_id, $token) {
        $discord_user_token_record = new DatabaseDiscordUserToken();
        
        $discord_user_token_record->setPropertiesFromOAuthToken($discord_user_id, $token);
        
        DatabaseUserTokens::save($discord_user_id, $discord_user_token_record);
    }
}