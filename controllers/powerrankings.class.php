<?php
/**
* The home page of the Necrolab.
* Copyright (c) 2015, Tommy Bolger
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
namespace Modules\Necrolab\Controllers;

use \Framework\Html\Table\DataTable;
use \Modules\Necrolab\Models\PowerRankings as PowerRankingsModel;

class PowerRankings
extends NecroLab {   
    protected $title = 'Power Rankings';

    protected $active_page;
 
    public function __construct() {
        parent::__construct();
        
        $this->active_page = 'power_rankings';
    }
    
    protected function constructContent() {
        $this->page->body->addChild($this->getDataTable(), 'content');
    }
    
    protected function getDataTable() {
        if(!isset($this->page)) {
            $this->loadModule();
        }
    
        $resultset = PowerRankingsModel::getLatestRankings();      
        
        $data_table = new DataTable("power_rankings", false);
        
        $data_table->setNumberofColumns(25);
        
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-name.png\" /></div>",
                'colspan' => 2
            ),
            'speed' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-speed.png\" /></div>",
                'colspan' => 9,
            ),
            'score' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-score.png\" /></div>",
                'colspan' => 9,
            ),
            'ranks' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-ranks.png\" /></div>",
                'colspan' => 5,
            )
        ));
        
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-namebar.png\" /></div>",
                'colspan' => 2
            ),
            'speed' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-speedbar.png\" /></div>",
                'colspan' => 9,
            ),
            'score' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-scorebar.png\" /></div>",
                'colspan' => 9,
            ),
            'ranks' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-ranksbar.png\" /></div>",
                'colspan' => 5,
            )
        ));
        
        $data_table->setHeader(array(
            'rank' => '&nbsp;',
            'personaname' => '&nbsp;',
            'cadence_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/cadence.png\" />",
            'bard_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/bard.png\" />",
            'monk_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/monk.png\" />",
            'aria_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/aria.png\" />",
            'bolt_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/bolt.png\" />",
            'dove_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/dove.png\" />",
            'eli_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/eli.png\" />",
            'melody_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/melody.png\" />",
            'dorian_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/dorian.png\" />",
            'cadence_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/cadence.png\" />",
            'bard_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/bard.png\" />",
            'monk_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/monk.png\" />",
            'aria_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/aria.png\" />",
            'bolt_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/bolt.png\" />",
            'dove_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/dove.png\" />",
            'eli_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/eli.png\" />",
            'melody_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/melody.png\" />",
            'dorian_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/dorian.png\" />",
            'speed_total' => "<img src=\"{$this->page->getImagesHttpPath()}/speed.png\" />",
            'score_total' => "<img src=\"{$this->page->getImagesHttpPath()}/score.png\" />",
            'base' => "<img src=\"{$this->page->getImagesHttpPath()}/basic.png\" />",
            'weighted' => "<img src=\"{$this->page->getImagesHttpPath()}/weight.png\" />",
            'top_10_bonus' => "<img src=\"{$this->page->getImagesHttpPath()}/bonus.png\" />"
        ));
        
        $data_table->process($resultset);
        
        return $data_table;
    }
    
    public function apiLatestRankings() {
        $page_number = request()->get->getVariable('page', 'integer');
        
        if(empty($page_number)) {
            $page_number = 1;
        }
        
        $resultset = PowerRankingsModel::getLatestRankings($page_number, 5000);
        
        $resultset->process();

        return array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'pages' => $resultset->getTotalPages(),
            'current_page' => $page_number,
            'data' => $resultset->getData()
        );
    }
}