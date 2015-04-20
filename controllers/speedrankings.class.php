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
use \Modules\Necrolab\Models\SpeedRankings as SpeedRankingsModel;

class SpeedRankings
extends NecroLab {   
    protected $title = 'Speed Rankings';
 
    public function __construct() {
        parent::__construct();

        $this->active_page = 'speed_rankings';
    }
    
    protected function constructContent() {
        $this->page->body->addChild($this->getDataTable(), 'content');
    }
    
    protected function getDataTable() {
        if(!isset($this->page)) {
            $this->loadModule();
        }
    
        $resultset = SpeedRankingsModel::getLatestRankings();      
        
        $data_table = new DataTable("speed_rankings", false);
        
        $data_table->setNumberofColumns(15);
        
        $character_placeholder_image = "{$this->page->getImagesHttpPath()}/character_placeholder.png";
        
        $data_table->setHeader(array(
            'speed_rank' => 'Rank',
            'social_media' => '&nbsp;',
            'personaname' => 'Player',
            'cadence_speed_rank' => "<img class=\"cadence_header\" src=\"{$character_placeholder_image}\" />",
            'bard_speed_rank' => "<img class=\"bard_header\" src=\"{$character_placeholder_image}\" />",
            'monk_speed_rank' => "<img class=\"monk_header\" src=\"{$character_placeholder_image}\" />",
            'aria_speed_rank' => "<img class=\"aria_header\" src=\"{$character_placeholder_image}\" />",
            'bolt_speed_rank' => "<img class=\"bolt_header\" src=\"{$character_placeholder_image}\" />",
            'dove_speed_rank' => "<img class=\"dove_header\" src=\"{$character_placeholder_image}\" />",
            'eli_speed_rank' => "<img class=\"eli_header\" src=\"{$character_placeholder_image}\" />",
            'melody_speed_rank' => "<img class=\"melody_header\" src=\"{$character_placeholder_image}\" />",
            'dorian_speed_rank' => "<img class=\"dorian_header\" src=\"{$character_placeholder_image}\" />",
            'all_speed_rank' => "All Characters",
            'story_speed_rank' => "Story Mode",
            'speed_rank_points_total' => 'Total Points'
        ));
        
        $data_table->process($resultset, array($this, 'addSocialMediaToTable'));
        
        return $data_table;
    }
    
    public function apiLatestRankings() {
        $page_number = request()->get->getVariable('page', 'integer');
        
        if(empty($page_number)) {
            $page_number = 1;
        }
        
        $resultset = SpeedRankingsModel::getLatestRankings($page_number, 100);
        
        $resultset->process();

        return array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'pages' => $resultset->getTotalPages(),
            'current_page' => $page_number,
            'data' => $resultset->getData()
        );
    }
}