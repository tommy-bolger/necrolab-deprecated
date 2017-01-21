<?php
/**
* The daily season page of Necrolab.
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
namespace Modules\Necrolab\Controllers\Page\Dailies;

use \Modules\Necrolab\Models\Dailies\Rankings\Database\Entries as DailyRankingEntriesModel;
use \Framework\Html\Misc\TemplateElement;

class Rankings
extends Dailies {   
    protected $title = 'All Time Rankings';
    
    protected $table_finishes_title = 'Top Season Finishes';
 
    public function init() {
        parent::init();
    
        $this->active_page = 'season';
        
        if(!empty($this->day_type)) {
            $this->active_page = "{$this->day_type}d";
        }
        else {
            $this->active_page = 'all_time';
        }
    }
    
    public function action() {    
        $dailies_template = new TemplateElement('dailies.php');

        $dailies_template->addChild($this->getDataTable(), 'data_table');
    
        $this->page->body->addChild($dailies_template, 'content');
    }
    
    protected function getDataTableResultSet() {
        return DailyRankingEntriesModel::getEntriesDisplayResultset($this->date, $this->day_type);   
    }
    
    /*protected function getApiResultset() {
        $page_number = request()->get->getVariable('page', 'integer');
        
        if(empty($page_number)) {
            $page_number = 1;
        }
    
        return DailyRankings::getRankingsResultset($page_number, 5000, $this->day_type);
    }*/
}