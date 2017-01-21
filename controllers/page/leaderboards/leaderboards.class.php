<?php
/**
* The base class for all leaderboard listing pages in Necrolab.
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

use \Framework\Core\Loader;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Table\Table;
use \Modules\Necrolab\Controllers\Page\Necrolab;
use \Modules\Necrolab\Models\Characters\Database\Characters;

class Leaderboards
extends Necrolab {
    public function __construct() {
        parent::__construct();

        $this->active_page_category = 'leaderboards';
    }
    
    public function setup() {
        parent::setup();
        
        $this->page->addCssFiles(array(
            'characters_header.css',
            'page/leaderboards_home.css'
        ));
    }    
    
    protected function getLeaderboardTable($category_name, $grouped_leaderboards) {
        $characters = Characters::getAllBySortOrder();
        $character_placeholder_image = $this->getCharacterImagePlaceholderUrl();
        $base_leaderboard_url = "/leaderboards/{$category_name}/entries?id=";
    
        $leaderboards_table = new Table("{$category_name}_leaderboards");
        
        $leaderboards_table->addClass('leaderboard_category');
            
        $leaderboards_table->setNumberofColumns(14);

        foreach($grouped_leaderboards as $grouped_leaderboard) {           
            $leaderboard_row = array(
                'name' => $grouped_leaderboard['name']
            );
                
            foreach($characters as $character) {
                $character_name = $character['name'];

                if(!empty($grouped_leaderboard['characters'][$character_name])) {
                    $link_display = '';
                    
                    switch($character_name) {   
                        case 'all':
                            $link_display = 'All<br />Chars';
                            break;
                        case 'story':
                            $link_display = 'Story<br />Mode';
                            break;
                        default:
                            $link_display = "<img class=\"{$character_name}_header\" src=\"{$character_placeholder_image}\" />";
                            break;
                    }

                    $leaderboard_row[$character_name] = "<a href=\"{$base_leaderboard_url}{$grouped_leaderboard['characters'][$character_name]}\">{$link_display}</a>";
                }
                else {
                    $leaderboard_row[$character_name] = "<img src=\"{$character_placeholder_image}\" />";
                }
            }
            
            $leaderboards_table->addRow($leaderboard_row);
        }
        
        return $leaderboards_table;
    }
}