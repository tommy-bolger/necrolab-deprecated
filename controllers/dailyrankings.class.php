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

use \Framework\Core\Controller;
use \Framework\Utilities\Http;
use \Framework\Modules\ModulePage;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Table\DataTable;
use \Framework\Data\ResultSet\SQL;

class DailyRankings
extends Home {    
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
    
        $resultset = new SQL('daily_rankings');
        
        $resultset->setBaseQuery("
            SELECT
                dre.rank,
                su.personaname,
                dre.first_place_ranks,
                dre.top_5_ranks,
                dre.top_10_ranks,
                dre.top_20_ranks,
                dre.top_50_ranks,
                dre.top_100_ranks,
                dre.total_points,
                dre.points_per_day,
                dre.total_dailies,
                dre.total_wins,
                dre.average_place                
            FROM daily_rankings dr
            JOIN daily_ranking_entries dre ON dre.daily_ranking_id = dr.daily_ranking_id
            JOIN steam_users su ON su.steam_user_id = dre.steam_user_id
            WHERE dr.latest = 1
            {{WHERE_CRITERIA}}
        ");
        
        //Set default sort criteria
        $resultset->setSortCriteria('dre.rank', 'ASC');
        
        //Set default rows per page
        $resultset->setRowsPerPage(100);
        
        $data_table = new DataTable("daily_rankings", true);
        
        $data_table->setNumberofColumns(13);
        
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-name.png\" /></div>",
                'colspan' => 2
            ),
            'speed' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-topfinishes.png\" /></div>",
                'colspan' => 8,
            ),
            'score' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-stats.png\" /></div>",
                'colspan' => 3,
            ),
        ));
        
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-namebar.png\" /></div>",
                'colspan' => 2
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
        
        $data_table->addFilterTextbox('personaname', 'su.personaname = ?', 'Contains', 'personaname');
        
        $data_table->process($resultset, function($query_rows) {
            if(!empty($query_rows)) {            
                foreach($query_rows as $row_index => $query_row) {
                    if(strlen($query_row['personaname']) > 14) {
                        $query_row['personaname'] = substr($query_row['personaname'], 0, 14) . '...';
                    }
                    
                    $query_rows[$row_index] = $query_row;
                }
            }
            
            return $query_rows;
        });
        
        return $data_table;
    }
    
    public function updateTableState() {
        $data_table = $this->getDataTable();
        
        return $data_table->toJsonArray();
    }
}