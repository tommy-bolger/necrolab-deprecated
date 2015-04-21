<?php
/**
* The daily rankings page.
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
use \Modules\Necrolab\Models\DailyRankings as DailyRankingsModel;

class DailyRankings
extends Necrolab {    
    protected $title = 'Daily Rankings';

    public function __construct() {
        parent::__construct();
        
        $this->active_page = 'daily_rankings';
    }
    
    protected function constructContent() {
        $this->page->body->addChild($this->getDataTable(), 'content');
    }
    
    protected function getDataTable() {
        if(!isset($this->page)) {
            $this->loadModule();
        }
        
        $resultset = DailyRankingsModel::getLatestRankings();   
        
        $data_table = new DataTable("daily_rankings", false);
        
        $data_table->setNumberofColumns(13);
        
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "<div class=\"center large_table_header\">Name</div>",
                'colspan' => 3
            ),
            'speed' => array(
                'contents' => "<div class=\"center large_table_header\">Top Finishes</div>",
                'colspan' => 8,
            ),
            'score' => array(
                'contents' => "<div class=\"center large_table_header\">Stats</div>",
                'colspan' => 3,
            ),
        ));
        
                
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "&nbsp;",
                'colspan' => 3
            ),
            'speed' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-topfinishesbar.png\" /></div>",
                'colspan' => 8,
            ),
            'score' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-statsbar.png\" /></div>",
                'colspan' => 3,
            )
        ));
        
        $data_table->setHeader(array(
            'rank' => '&nbsp;',
            'social_media' => '&nbsp;',            
            'personaname' => 'Player',
            'first_place_ranks' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-1st.png\" />",
            'top_5_ranks' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-top5.png\" />",
            'top_10_ranks' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-top10.png\" />",
            'top_20_ranks' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-top20.png\" />",
            'top_50_ranks' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-top50.png\" />",
            'top_100_ranks' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-top100.png\" />",
            'total_points' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-total.png\" />",
            'points_per_day' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-avg.png\" />",
            'total_dailies' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-totaldailies.png\" />",
            'total_wins' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-totalwins.png\" />",
            'average_place' => "<img src=\"{$this->page->getImagesHttpPath()}/sort-avgplace.png\" />"
        ));
        
        $filter_textbox = $data_table->addFilterTextbox('personaname', '*?*', NULL, 'personaname');
        
        $filter_textbox->setAttribute('placeholder', 'Search');
        
        $data_table->process($resultset, function($result_data) {
            if(!empty($result_data)) {
                foreach($result_data as $index => $row) {
                    $row = $this->addSocialMediaToRow($row);
                    
                    $result_data[$index] = $row;
                }
            }
            
            return $result_data;
        });
        
        return $data_table;
    }
    
    public function apiLatestRankings() {
        $page_number = request()->get->getVariable('page', 'integer');
        
        if(empty($page_number)) {
            $page_number = 1;
        }
        
        $resultset = DailyRankingsModel::getLatestRankings($page_number, 5000);
        
        $resultset->process();

        return array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'pages' => $resultset->getTotalPages(),
            'current_page' => $page_number,
            'data' => $resultset->getData()
        );
    }
}