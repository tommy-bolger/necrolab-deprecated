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
use \Modules\Necrolab\Models\ScoreRankings as ScoreRankingsModel;

class ScoreRankings
extends NecroLab {   
    protected $title = 'Score Rankings';
 
    public function __construct() {
        parent::__construct();

        $this->active_page = 'score_rankings';
    }
    
    protected function constructContent() {
        $this->page->body->addChild($this->getDataTable(), 'content');
    }
    
    protected function getDataTable() {
        if(!isset($this->page)) {
            $this->loadModule();
        }
    
        $resultset = ScoreRankingsModel::getLatestRankings();      
        
        $data_table = new DataTable("score_rankings", true);
        
        $data_table->setNumberofColumns(15);
        
        $character_placeholder_image = "{$this->page->getImagesHttpPath()}/character_placeholder.png";
        
        $data_table->setHeader(array(
            'score_rank' => 'Rank',
            'social_media' => '&nbsp;',
            'personaname' => 'Player',
            'cadence_score_rank' => "<img class=\"cadence_header\" src=\"{$character_placeholder_image}\" />",
            'bard_score_rank' => "<img class=\"bard_header\" src=\"{$character_placeholder_image}\" />",
            'monk_score_rank' => "<img class=\"monk_header\" src=\"{$character_placeholder_image}\" />",
            'aria_score_rank' => "<img class=\"aria_header\" src=\"{$character_placeholder_image}\" />",
            'bolt_score_rank' => "<img class=\"bolt_header\" src=\"{$character_placeholder_image}\" />",
            'dove_score_rank' => "<img class=\"dove_header\" src=\"{$character_placeholder_image}\" />",
            'eli_score_rank' => "<img class=\"eli_header\" src=\"{$character_placeholder_image}\" />",
            'melody_score_rank' => "<img class=\"melody_header\" src=\"{$character_placeholder_image}\" />",
            'dorian_score_rank' => "<img class=\"dorian_header\" src=\"{$character_placeholder_image}\" />",
            'all_score_rank' => "All Characters",
            'story_score_rank' => "Story Mode",
            'score_rank_points_total' => 'Total Points'
        ));
        
        $filter_textbox = $data_table->addFilterTextbox('personaname', '*?*', NULL);
        
        $filter_textbox->setAttribute('placeholder', 'Search Players');
        
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
        
        $resultset = ScoreRankingsModel::getLatestRankings($page_number, 100);
        
        $resultset->addProcessorFunction(function($data) {
            $api_response_data = array();
        
            if(!empty($data)) {
                foreach($data as &$row) {
                    $api_response_data[] = array(
                        'rank' => $row['score_rank'],
                        'points' => $row['score_rank_points_total'],
                        'steam_id' => $row['steamid'],  
                        'steam_username' => $row['personaname'],
                        'twitch_username' => $row['twitch_username'],
                        'twitter_username' => $row['twitter_username'],
                        'website' => $row['website'],
                        'cadence_rank' => $row['cadence_score_rank'],
                        'cadence_points' => $row['cadence_score_rank_points'],
                        'bard_rank' => $row['bard_score_rank'],
                        'bard_points' => $row['bard_score_rank_points'],
                        'monk_rank' => $row['monk_score_rank'],
                        'monk_points' => $row['monk_score_rank_points'],
                        'aria_rank' => $row['aria_score_rank'],
                        'aria_points' => $row['aria_score_rank_points'],
                        'bolt_rank' => $row['bolt_score_rank'],
                        'bolt_points' => $row['bolt_score_rank_points'],
                        'dove_rank' => $row['dove_score_rank'],
                        'dove_points' => $row['dove_score_rank_points'],
                        'eli_rank' => $row['eli_score_rank'],
                        'eli_points' => $row['eli_score_rank_points'],        
                        'melody_rank' => $row['melody_score_rank'],
                        'melody_points' => $row['melody_score_rank_points'],
                        'dorian_rank' => $row['dorian_score_rank'],
                        'dorian_points' => $row['dorian_score_rank_points'],
                        'all_character_rank' => $row['all_score_rank'],
                        'all_character_points' => $row['all_score_rank_points'],
                        'story_mode_rank' => $row['story_score_rank'],
                        'story_mode_points' => $row['story_score_rank_points']
                    );
                }
            }
            
            return $api_response_data;
        });
        
        $resultset->process();

        return array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'pages' => $resultset->getTotalPages(),
            'current_page' => $page_number,
            'data' => $resultset->getData()
        );
    }
}