<?php
/**
* The api documentation page of the about section of Necrolab.
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
namespace Modules\Necrolab\Controllers\Page\About;

use \Framework\Html\Misc\TemplateElement;

class Api
extends About {    
    public function init() {
        parent::init();
        
        $this->addBreadCrumb('API', '/about/api');
        
        $this->active_page = 'api';
        
        $this->title = "API";
    }

    public function actionGet() {                    
        $leaderboards_content = new TemplateElement('/api/leaderboards.php');
        $rankings_content = new TemplateElement('/api/rankings.php');
        $players_content = new TemplateElement('/api/players.php');
        $achievements_content = new TemplateElement('/api/achievements.php');
        $pbs_content = new TemplateElement('/api/pbs.php');
        $player_leaderboards_content = new TemplateElement('/api/player_leaderboards.php');
        $player_rankings_content = new TemplateElement('/api/player_rankings.php');
        
        $page_content = new TemplateElement('/api/main.php');
        
        $page_content->addChild($leaderboards_content, 'leaderboards');
        $page_content->addChild($rankings_content, 'rankings');
        $page_content->addChild($players_content, 'players');
        $page_content->addChild($achievements_content, 'achievements');
        $page_content->addChild($pbs_content, 'pbs');
        $page_content->addChild($player_leaderboards_content, 'player_leaderboards');
        $page_content->addChild($player_rankings_content, 'player_rankings');
        
        $this->page->body->addChild($page_content, 'content');
    }
}