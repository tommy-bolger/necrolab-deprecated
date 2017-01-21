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
namespace Modules\Necrolab\Controllers\Page\Rankings\Categories;

use \Modules\Necrolab\Controllers\Page\Rankings\Rankings;
use \Modules\Necrolab\Models\Necrolab as NecrolabModel;

class Categories
extends Rankings {    
    public function setup() {
        parent::setup();
        
        $this->page->addCssFiles(array(
            'characters_header.css'
        ));
    }
    
    protected function getTableHeader($ranking_type) {
        if(!isset($this->page)) {
            $this->loadModule();
        }
    
        switch($ranking_type) {
            case 'score':
            case 'speed':
            case 'deathless':
                break;
            default:
                throw new Exception("Data type '{$ranking_type}' is invalid. Please specify only 'score', 'speed', or 'deathless'.");
                break;
        }
        
        $character_placeholder_image = $this->getCharacterImagePlaceholderUrl();
        
        $data_table_header = array(
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
            'coda_score_rank' => "<img class=\"coda_header\" src=\"{$character_placeholder_image}\" />",
            'all_score_rank' => "All<br />Chars",
            'story_score_rank' => "Story<br />Mode",
            'total_points' => 'Total<br />Points'
        );
        
        if($ranking_type == 'deathless') {
            unset($data_table_header['all_score_rank']);
            unset($data_table_header['story_score_rank']);
        }
        
        return $data_table_header;
    }
    
    protected function processTableData($ranking_type, array $result_data) {
        switch($ranking_type) {
            case 'score':
            case 'speed':
            case 'deathless':
                break;
            default:
                throw new Exception("Data type '{$ranking_type}' is invalid. Please specify only 'score', 'speed', or 'deathless'.");
                break;
        }
        
        $processed_data = array();
        
        if(!empty($result_data)) {
            foreach($result_data as $index => $row) {
                $processed_row = array(
                    'rank' => $row['rank'],
                    'social_media' => $this->getSocialMedia($row),
                    'personaname' => $this->getUsernameLink($row['personaname'], $row['steamid']),
                    'cadence_rank' => $row['cadence_rank'],
                    'bard_rank' => $row['bard_rank'],
                    'monk_rank' => $row['monk_rank'],
                    'aria_rank' => $row['aria_rank'],
                    'bolt_rank' => $row['bolt_rank'],
                    'dove_rank' => $row['dove_rank'],
                    'eli_rank' => $row['eli_rank'],
                    'melody_rank' => $row['melody_rank'],
                    'dorian_rank' => $row['dorian_rank'],
                    'coda_rank' => $row['coda_rank'],
                    'story_rank' => NULL,
                    'all_rank' => NULL,
                    'total_points' => NecrolabModel::roundNumber($row['total_points'])
                );
                
                if($ranking_type != 'deathless') {
                    $processed_row['story_rank'] = $row['story_rank'];
                    $processed_row['all_rank'] = $row['all_rank'];
                }
                else {
                    unset($processed_row['story_rank']);
                    unset($processed_row['all_rank']);
                }
                
                $processed_data[] = $processed_row;
            }
        }
        
        return $processed_data;
    }
}