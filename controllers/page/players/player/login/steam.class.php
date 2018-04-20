<?php
/**
* The login controller for Steam users.
* Copyright (c) 2017, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/
namespace Modules\Necrolab\Controllers\Page\Players\Player\Login;

use \Exception;
use \Framework\Core\Loader;
use \Framework\Utilities\Http;
use \Modules\Necrolab\Controllers\Page\Players\Player\Player;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as SteamUsersModel;
use \LightOpenID;

class Steam
extends Player {
    protected $open_id;

    public function setup() {}
    
    public function init() {
        $this->setSteamId();
        
        Loader::load('openid.php');

        $this->open_id = new LightOpenID(Http::getBaseUrl());
    }
    
    public function actionPost() {        
        if(request()->post->steam_login_submitted == 1 && !empty($this->steamid)) {
            session()->end();
        
            $this->open_id->identity = 'https://steamcommunity.com/openid';

            Http::redirect($this->open_id->authUrl());
        }

        Http::redirect('/players/');
    }
    
    public function actionGet() {
        $openid_mode = request()->get->{'openid.mode'};
        
        if(!empty($openid_mode) && $openid_mode != 'cancel' && !empty($this->open_id->validate())) {
            $openid_steamid = SteamUsersModel::getIdFromOpenIdIdentity(request()->{'openid.identity'});
            
            if($openid_steamid == $this->steamid) {
                $steam_user_record = SteamUsersModel::get($this->steamid);

                if(!empty($steam_user_record)) {
                    session()->steamid = $this->steamid;
                    
                    session()->beampro_user_id = $steam_user_record['beampro_user_id'];
                    session()->discord_user_id = $steam_user_record['discord_user_id'];
                    session()->reddit_user_id = $steam_user_record['reddit_user_id'];
                    session()->twitch_user_id = $steam_user_record['twitch_user_id'];
                    session()->twitter_user_id = $steam_user_record['twitter_user_id'];
                    session()->youtube_user_id = $steam_user_record['youtube_user_id'];
                    
                    Http::redirect(Http::generateUrl('/players/player', array(
                        'id' => $this->steamid
                    )));
                }
            }
        }
    
        Http::redirect('/players/');
    }
}