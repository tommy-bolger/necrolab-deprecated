<?php
/**
* The base entries page for leaderboards in Necrolab.
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
namespace Modules\Necrolab\Controllers\Page\Leaderboards;

use \Exception;
use \Framework\Html\Table\DataTable;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Utilities\Http;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards as LeaderboardsModel;
use \Modules\Necrolab\Models\Leaderboards\Database\Entries as LeaderboardEntriesModel;

class Entries
extends Leaderboards {   
    protected $title = 'Leaderboards';
    
    protected $lbid;
    
    protected $leaderboard_record;
    
    protected $leaderboard_type;
    
    protected function loadLeaderboard() {
        $this->lbid = request()->id;
        
        if(empty($this->lbid)) {
            Http::redirect('/leaderboards/');
        }
        
        $this->leaderboard_record = LeaderboardsModel::get($this->lbid);
        
        if(empty($this->leaderboard_record)) {
            Http::redirect('/leaderboards/');
        }
        
        if($this->leaderboard_record['is_deathless'] == 1) {
            $this->leaderboard_type = 'deathless';
        }
        else {
            if($this->leaderboard_record['is_score_run'] == 1) {
                $this->leaderboard_type = 'score';
            }
            elseif($this->leaderboard_record['is_speedrun'] == 1) {
                $this->leaderboard_type = 'speed';
            }
        }
    }
    
    public function init() {
        parent::init();
        
        $this->active_page_category = 'leaderboards';
        
        $this->loadLeaderboard();
    }
    
    public function setup() {
        parent::setup();
        
        $this->page->addCssFiles(array(
            'characters_header.css'
        ));
    }
    
    public function action() {  
        $this->page->body->addChild("{$this->leaderboard_type}_leaderboards", 'active_page');
        
        $character_placeholder_image_url = $this->getCharacterImagePlaceholderUrl();
        $leaderboard_name = LeaderboardsModel::getFancyName($this->leaderboard_record);
        
        $has_character_image = true;
        
        if($this->leaderboard_record['is_story_mode'] == 1 || $this->leaderboard_record['is_all_character'] == 1) {
            $has_character_image = false;
        }
    
        $rankings_view_template = new TemplateElement("leaderboard_rankings_view.php");
        
        $rankings_view_template->addChild($this->getDataTable(), 'rankings_table');
        $rankings_view_template->addChild($character_placeholder_image_url, 'character_placeholder_image_url');
        $rankings_view_template->addChild($this->leaderboard_record['character_name'], 'character_name');
        $rankings_view_template->addChild($has_character_image, 'has_character_image');
        $rankings_view_template->addChild($leaderboard_name, 'leaderboard_name');
    
        $this->page->body->addChild($rankings_view_template, 'content');
    }
    
    protected function getTableHeader() {
        return array(
            'rank' => 'Rank',
            'social_media' => '&nbsp;',
            'personaname' => 'Player'
        );
    }
    
    protected function getTableRow(array $row) {
        return array(
            'rank' => $row['rank'],
            'social_media' => $this->getSocialMedia($row),
            'personaname' => $this->getUsernameLink($row['personaname'], $row['steamid'])
        );
    }
    
    protected function getReplayLink($replay_url) {
        $username_html = new Template('replay_download.php');
        
        $username_html->setPlaceholderValues(array(
            'replay_url' => $replay_url
        ));
    
        return $username_html->parseTemplate();
    }
    
    protected function getResultset() {
        $resultset = LeaderboardEntriesModel::getEntriesDisplayResultset($this->date, $this->lbid);  
        
        $resultset->setRowsPerPage(100);
        
        return $resultset;
    }
    
    protected function getDataTable() {    
        $resultset = $this->getResultset();
        
        $data_table = new DataTable("leaderboard_rankings", false);
        
        $data_table->disableJavascript();
        
        $data_table->addRequestVariable('id', $this->lbid);
        
        $data_table->addRequestVariable('date', $this->date->format('Y-m-d'));
        
        $header = $this->getTableHeader();
        
        $data_table->setHeader($header);
        
        $data_table->setNumberofColumns(count($header));
        
        $filter_textbox = $data_table->addFilterTextbox('personaname', '*?*', NULL);
        
        $filter_textbox->setAttribute('placeholder', 'Search Players');
        
        $data_table->process($resultset, function($result_data) {
            $processed_data = array();
        
            if(!empty($result_data)) {
                foreach($result_data as $row) {
                    $processed_data[] = $this->getTableRow($row);
                }
            }
            
            return $processed_data;
        });
        
        return $data_table;
    }
    
    /*public function apiGetRankings() {        
        $page_number = request()->get->getVariable('page', 'integer');
        
        if(empty($page_number)) {
            $page_number = 1;
        }
        
        $resultset = LeaderboardsModel::getRankingsResultset($this->lbid, $page_number, 100);
        
        $resultset->process();

        return array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'pages' => $resultset->getTotalPages(),
            'current_page' => $page_number,
            'data' => $resultset->getData()
        );
    }*/
}