<?php
/**
* The player listing page in Necrolab.
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
namespace Modules\Necrolab\Controllers\Page\Players;

use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Table\DataTable;
use \Modules\Necrolab\Controllers\Page\Necrolab;
use \Modules\Necrolab\Models\SteamUsers\Database\SteamUsers as SteamUsersModel;
use \Modules\Necrolab\Models\TwitchUsers\TwitchUsers as TwitchUsersModel;
use \Modules\Necrolab\Models\TwitterUsers\TwitterUsers as TwitterUsersModel;
use \Modules\Necrolab\Models\HitboxUsers\HitboxUsers as HitboxUsersModel;

class Players
extends Necrolab {    
    public function init() {
        parent::init();
        
        $this->active_page_category = 'players';
    }
    
    public function setup() {
        parent::setup();
        
        $this->addDataTableFiles();
        
        $this->page->addJavascriptFiles(array(
            '/tables/players.js'
        ));
    }
    
    public function actionGet() {            
        $entries_table = new TemplateElement('entries_table.php');
        
        $this->page->body->addChild($entries_table, 'content');
    }
}