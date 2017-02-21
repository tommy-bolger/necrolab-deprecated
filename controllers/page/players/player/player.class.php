<?php
/**
* The user stats page.
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
namespace Modules\Necrolab\Controllers\Page\Players\Player;

use \Exception;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Form\Form;
use \Modules\Necrolab\Controllers\Page\Players\Players;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as SteamUsersModel;

class Player
extends Players {    
    protected $steamid;
    
    protected $steam_user_record = array();
    
    public function init() {
        parent::init();
        
        $this->setSteamId();
        
        if(empty($this->steamid)) {
            Http::redirect('/players/');
        }
        
        $this->steam_user_record = SteamUsersModel::get($this->steamid);

        if(empty($this->steam_user_record)) {
            Http::redirect('/players/');
        }
    }
    
    protected function setSteamId() {
        $this->steamid = request()->get->getVariable('id', 'integer');
    }
    
    public function setup() {
        $this->title = "Profile for {$this->steam_user_record['personaname']}";
    
        parent::setup();
        
        $this->page->addCssFiles(array(
            'page/player.css',
            'characters_header.css'
        ));
        
        $this->page->addJavascriptFiles(array(
            'tables/player.js'
        ));
        
        if(isset(session()->steamid)) {
            $this->page->addInlineJavascript("
                $(document).ready(function() {
                    NecroTable.user_api_key = true;
                });
            ");
        }
    }
    
    public function actionGet() {     
        $user_template = new TemplateElement("player.php");
        
        $user_template->addChild($this->steam_user_record, 'steam_user_record');

        if(isset(session()->steamid) && $this->steamid == session()->steamid) {
  
        }
        else {            
            $steam_login_form = new Form('steam_login', Http::generateUrl('/players/player/login/steam', array(
                'id' => $this->steamid
            )), 'post', false);
            $steam_login_form->disableJavascript();
            $steam_login_form->addHidden('id', $this->steamid);
            $steam_login_form->addImageButton('login_button', "{$this->page->getThemeImagesHttpPath()}/connections/steam_login_small.png");

            $user_template->addChild($steam_login_form, 'steam_login_form');
        }
        
        $this->page->body->addChild($user_template, 'content');
    }
}