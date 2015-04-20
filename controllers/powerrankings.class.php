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
use \Framework\Utilities\Http;
use \Modules\Necrolab\Models\PowerRankings as PowerRankingsModel;

class PowerRankings
extends NecroLab {   
    protected $title = 'Power Rankings';
 
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
        
        $data_table->setNumberofColumns(10);
        
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "&nbsp;",
                'colspan' => 3
            ),
            'score' => array(
                'contents' => "<div class=\"center menu_small\">Score</div>",
                'colspan' => 2,
            ),
            'speed' => array(
                'contents' => "<div class=\"center menu_small\">Speed</div>",
                'colspan' => 2,
            ),
            'deathless_score' => array(
                'contents' => "<div class=\"center menu_small\">Deathless</div>",
                'colspan' => 2,
            ),
            'total' => array(
                'contents' => "&nbsp;"
            )
        ));
        
        $data_table->setHeader(array(
            'rank' => 'Rank',
            'social_media' => '&nbsp;',
            'personaname' => 'Player',
            'score_rank' => '<span class="no_wrap">Rank</span>',
            'score_rank_points_total' => '<span class="no_wrap">Points</span>',
            'speed_rank' => '<span class="no_wrap">Rank</span>',
            'speed_rank_points_total' => '<span class="no_wrap">Points</span>',            
            'deathless_score_rank' => '<span class="no_wrap">Rank</span>',
            'deathless_score_rank_points_total' => '<span class="no_wrap">Points</span>',
            'total_points' => '<span class="no_wrap">Total Points</span>'            
        ));
        
        $data_table->process($resultset, array($this, 'addSocialMediaToTable'));
        
        return $data_table;
    }
    
    public function apiLatestRankings() {
        $page_number = request()->get->getVariable('page', 'integer');
        
        if(empty($page_number)) {
            $page_number = 1;
        }
        
        $resultset = PowerRankingsModel::getLatestRankings($page_number, 100);
        
        $resultset->process();

        return array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'pages' => $resultset->getTotalPages(),
            'current_page' => $page_number,
            'data' => $resultset->getData()
        );
    }
}