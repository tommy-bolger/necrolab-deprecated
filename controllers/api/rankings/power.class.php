<?php
/**
* The home page of the Necrolab.
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
namespace Modules\Necrolab\Controllers\Page\Rankings;

use \Framework\Html\Table\DataTable;
use \Framework\Utilities\Http;
use \Modules\Necrolab\Models\Rankings\Database\Power as PowerRankingsModel;

class Power
extends Rankings {   
    protected $title = 'Power Rankings';
    
    public function init() {
        parent::init();
    
        $this->active_page_category = 'rankings';
        $this->active_page = 'power_rankings';
    }
    
    public function action() {    
        $this->page->body->addChild($this->getDataTable(), 'content');
    }
    
    protected function getDataTable() {    
        $resultset = PowerRankingsModel::getEntriesResultset($this->date);
        $resultset->setRowsPerPage(100);
        
        $data_table = new DataTable("power_rankings", false);
        
        $data_table->disableJavascript();
        
        $data_table->setNumberofColumns(10);
        
        $data_table->addRequestVariable('date', $this->date->format('Y-m-d'));
        
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "&nbsp;",
                'colspan' => 3
            ),
            'score' => array(
                'contents' => "<div class=\"center menu_small\">Score</div>",
                'classes' => array(
                    'group_header_column',
                    'group_header_column_first'
                ),
                'colspan' => 2,
            ),
            'speed' => array(
                'contents' => "<div class=\"center menu_small\">Speed</div>",
                'classes' => 'group_header_column',
                'colspan' => 2,
            ),
            'deathless' => array(
                'contents' => "<div class=\"center menu_small\">Deathless</div>",
                'classes' => 'group_header_column',
                'colspan' => 2
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
            'deathless_rank' => '<span class="no_wrap">Rank</span>',
            'deathless_rank_points_total' => '<span class="no_wrap">Points</span>',
            'total_points' => '<span class="no_wrap">Total<br />Points</span>'            
        ));
        
        $filter_textbox = $data_table->addFilterTextbox('personaname', "su.personaname ILIKE '%?%'", NULL);
        
        $filter_textbox->setAttribute('placeholder', 'Search Players');
        
        $data_table->process($resultset, function($result_data) {
            $processed_data = array();

            if(!empty($result_data)) {
                foreach($result_data as $row) {
                    $processed_data[] = array(
                        'rank' => $row['rank'],
                        'social_media' => $this->getSocialMedia($row),
                        'personaname' => $this->getUsernameLink($row['personaname'], $row['steamid']),
                        'score_rank' => $row['score_rank'],
                        'score_rank_points_total' => PowerRankingsModel::roundNumber($row['score_rank_points_total']),
                        'speed_rank' => $row['speed_rank'],
                        'speed_rank_points_total' => PowerRankingsModel::roundNumber($row['speed_rank_points_total']),
                        'deathless_rank' => $row['deathless_rank'],
                        'deathless_rank_points_total' => PowerRankingsModel::roundNumber($row['deathless_rank_points_total']),
                        'total_points' => PowerRankingsModel::roundNumber($row['total_points'])
                    );
                }
            }
            
            return $processed_data;
        });
        
        return $data_table;
    }
    
    /*public function apiLatestRankings() {
        $page_number = request()->get->getVariable('page', 'integer');
        
        if(empty($page_number)) {
            $page_number = 1;
        }
        
        $resultset = PowerRankingsModel::getRankingsResultset($page_number, 100);
        
        $resultset->process();

        return array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'pages' => $resultset->getTotalPages(),
            'current_page' => $page_number,
            'data' => $resultset->getData()
        );
    }*/
}