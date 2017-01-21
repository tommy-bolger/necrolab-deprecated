<?php
/**
* The daily season page of Necrolab.
* Copyright (c) 2016, Tommy Bolger
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
namespace Modules\Necrolab\Controllers\Dailies;

use \Modules\Necrolab\Models\DailySeasons\Cache\DailySeasons;
use \Framework\Html\Misc\TemplateElement;

class Season
extends Dailies {   
    protected $title = 'Season Rankings';
    
    protected $table_finishes_title = 'Top Season Finishes';
    
    protected $season;
 
    public function __construct() {
        parent::__construct();

        $this->active_page = 'season';
    }
    
    protected function constructContent() {    
        $dailies_template = new TemplateElement('dailies.php');
        
        $season_number = request()->get->s;
        
        if(!empty($season_number)) {
            $this->season = DailySeasons::getSeason($season_number);
        }
        
        if(empty($season)) {
            $this->season = DailySeasons::getLatestSeason();
        }
        
        $content_title = DailySeasons::getSeasonFancyName($this->season);
        
        $dailies_template->addChild($content_title, 'content_title');
        
        $dailies_template->addChild($this->getDataTable(), 'data_table');
    
        $this->page->body->addChild($dailies_template, 'content');
    }
    
    protected function getDataTableResultSet() {
        return DailySeasons::getRankingsResultset($this->season['season_number']);   
    }
    
    protected function getApiResultset() {
        $page_number = request()->get->getVariable('page', 'integer');
        
        if(empty($page_number)) {
            $page_number = 1;
        }
    
        return DailySeasons::getRankingsResultset($page_number, 5000);
    }
}