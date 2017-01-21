<?php
/**
* The daily rankings page.
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

use \Framework\Html\Table\DataTable;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\Rankings as DailyRankingsModel;
use \Modules\Necrolab\Controllers\Page\Necrolab;

class Dailies
extends Necrolab {    
    protected $title = 'Daily Rankings';
    
    protected $table_finishes_title;
    
    protected $day_type;
    
    public function init() {
        parent::init();
        
        $this->active_page_category = 'dailies';
        
        $this->day_type = request()->dt;
    }
    
    protected function getLastRefreshed() {
        return cache()->get('last_updated', 'daily_rankings');
    }
    
    protected function constructContent() {
        $this->page->body->addChild($this->getDataTable(), 'content');
    }
    
    protected function getDataTableResultSet() {}
    
    protected function getDataTable() {        
        $resultset = $this->getDataTableResultSet();
        
        $data_table = new DataTable("daily_rankings", false);
        
        $data_table->disableJavascript();
        
        if(!empty($this->day_type)) {
            $data_table->addRequestVariable('dt', $this->day_type);
        }
        
        $data_table->addRequestVariable('date', $this->date->format('Y-m-d'));
        
        $data_table->setNumberofColumns(14); 
        
        $images_http_path = $this->page->getImagesHttpPath();
        
        $data_table->setHeader(array(
            'rank' => '&nbsp;',
            'social_media' => '&nbsp;',            
            'personaname' => 'Player',
            'first_place_ranks' => "<img src=\"{$images_http_path}/sort-1st.png\" />",
            'top_5_ranks' => "<img src=\"{$images_http_path}/sort-top5.png\" />",
            'top_10_ranks' => "<img src=\"{$images_http_path}/sort-top10.png\" />",
            'top_20_ranks' => "<img src=\"{$images_http_path}/sort-top20.png\" />",
            'top_50_ranks' => "<img src=\"{$images_http_path}/sort-top50.png\" />",
            'top_100_ranks' => "<img src=\"{$images_http_path}/sort-top100.png\" />",
            'total_points' => "<img src=\"{$images_http_path}/sort-total.png\" />",
            'points_per_day' => "<img src=\"{$images_http_path}/sort-avg.png\" />",
            'total_dailies' => "<img src=\"{$images_http_path}/sort-totaldailies.png\" />",
            'total_wins' => "<img src=\"{$images_http_path}/sort-totalwins.png\" />",
            'average_rank' => "<img src=\"{$images_http_path}/sort-avgplace.png\" />"
        ));
        
        $filter_textbox = $data_table->addFilterTextbox('personaname', '*?*', NULL);
        
        $filter_textbox->setAttribute('placeholder', 'Search Players');
        
        $data_table->process($resultset, function($result_data) {
            $processed_data = array();

            if(!empty($result_data)) {
                foreach($result_data as $index => $row) {
                    $processed_data[] = array(
                        'rank' => $row['rank'],
                        'social_media' => $this->getSocialMedia($row),
                        'personaname' => $this->getUsernameLink($row['personaname'], $row['steamid']),
                        'first_place_ranks' => $row['first_place_ranks'],
                        'top_5_ranks' => $row['top_5_ranks'],
                        'top_10_ranks' => $row['top_10_ranks'],
                        'top_20_ranks' => $row['top_20_ranks'],
                        'top_50_ranks' => $row['top_50_ranks'],
                        'top_100_ranks' => $row['top_100_ranks'],
                        'total_points' => DailyRankingsModel::roundNumber($row['total_points']),
                        'points_per_day' => DailyRankingsModel::roundNumber($row['points_per_day']),
                        'total_dailies' => $row['total_dailies'],
                        'total_wins' => $row['total_wins'],
                        'average_rank' => DailyRankingsModel::roundNumber($row['average_rank'])
                    );
                }
            }
            
            return $processed_data;
        });
        
        return $data_table;
    }
    
    /*protected function getApiResultset() {}
    
    public function apiLatestRankings() {        
        $resultset = $this->getApiResultset();
        
        $resultset->process();

        return array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'pages' => $resultset->getTotalPages(),
            'current_page' => $page_number,
            'data' => $resultset->getData()
        );
    }*/
}