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

use \Framework\Core\Controller;
use \Framework\Utilities\Http;
use \Framework\Modules\ModulePage;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Table\DataTable;
use \Framework\Data\ResultSet\SQL;

class Home
extends Controller {   
    protected $active_page;
 
    public function __construct() {
        parent::__construct();
        
        $this->active_page = 'home';
    }

    protected function loadModule() {
        $this->page = new ModulePage('necrolab', 'necrolab_home');
        
        $this->page->setTitle('NecroLab::Power Rankings');

        $this->page->addCssFiles(array(
            'reset.css',
            'bootstrap.css',
            'main.css'
        ));
    }

    public function setup() {    
        $this->loadModule();
        
        $this->page->setTemplate('home.php');
    
        $this->page->body->addChild("{$this->page->getImagesHttpPath()}/logotemp.png", 'site_logo');
        
        $menu_bar_url = '';
        
        //The power rankings button
        $menu_1_button = $this->page->getImagesHttpPath();

        if($this->active_page == 'home') {
            $menu_1_button .= '/menu1.png';
            
            $menu_bar_url  = "{$this->page->getImagesHttpPath()}/menubar1.png";
        }
        else {
            $menu_1_button .= '/menu1greyed.png';
        }

        $this->page->body->addChild($menu_1_button, 'power_rankings_button');
        
        //The daily rankings button
        $menu_2_button = $this->page->getImagesHttpPath();
        
        if($this->active_page == 'daily_rankings') {
            $menu_2_button .= '/menu2.png';
            
            $menu_bar_url  = "{$this->page->getImagesHttpPath()}/menubar2.png";
        }
        else {
            $menu_2_button .= '/menu2greyed.png';
        }

        $this->page->body->addChild($menu_2_button, 'daily_rankings_button');
        
        //The cool stats button
        $menu_3_button = $this->page->getImagesHttpPath();
        
        if($this->active_page == 'cool_stats') {
            $menu_3_button .= '/menu3.png';
            
            $menu_bar_url  = "{$this->page->getImagesHttpPath()}/menubar3.png";
        }
        else {
            $menu_3_button .= '/menu3greyed.png';
        }
        
        $this->page->body->addChild($menu_3_button, 'cool_stats_button');
        
        //The 7 character speedrun button
        $menu_4_button = $this->page->getImagesHttpPath();
        
        if($this->active_page == 'seven_character_speedruns') {
            $menu_4_button .= '/menu4.png';
            
            $menu_bar_url  = "{$this->page->getImagesHttpPath()}/menubar4.png";
        }
        else {
            $menu_4_button .= '/menu4greyed.png';
        }
        
        $this->page->body->addChild($menu_4_button, 'seven_character_speedrun_button');
        
        $this->page->body->addChild($menu_bar_url, 'menu_bar');

        $this->constructContent();
    }
    
    protected function constructContent() {
        $this->page->body->addChild($this->getDataTable(), 'content');
    }
    
    protected function getDataTable() {
        if(!isset($this->page)) {
            $this->loadModule();
        }
    
        $resultset = new SQL('power_rankings');
        
        $resultset->setBaseQuery("
            SELECT
                pre.rank,
                su.personaname,
                pre.cadence_speed_rank,
                pre.bard_speed_rank,
                pre.monk_speed_rank,
                pre.aria_speed_rank,
                pre.bolt_speed_rank,
                pre.dove_speed_rank,
                pre.eli_speed_rank,
                pre.melody_speed_rank,
                pre.dorian_speed_rank,
                pre.cadence_score_rank,
                pre.bard_score_rank,
                pre.monk_score_rank,
                pre.aria_score_rank,
                pre.bolt_score_rank,
                pre.dove_score_rank,
                pre.eli_score_rank,
                pre.melody_score_rank,
                pre.dorian_score_rank,
                pre.speed_total,
                pre.score_total,
                pre.base,
                pre.weighted,
                pre.top_10_bonus
            FROM power_rankings pr
            JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
            JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
            WHERE pr.latest = 1
            {{WHERE_CRITERIA}}
        ");
        
        //Set default sort criteria
        $resultset->setSortCriteria('pre.rank', 'ASC');
        
        //Set default rows per page
        $resultset->setRowsPerPage(25);
        
        $data_table = new DataTable("power_rankings", true);
        
        $data_table->setNumberofColumns(25);
        
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-name.png\" /></div>",
                'colspan' => 2
            ),
            'speed' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-speed.png\" /></div>",
                'colspan' => 9,
            ),
            'score' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-score.png\" /></div>",
                'colspan' => 9,
            ),
            'ranks' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-ranks.png\" /></div>",
                'colspan' => 5,
            )
        ));
        
        $data_table->addHeader(array(
            'name' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-namebar.png\" /></div>",
                'colspan' => 2
            ),
            'speed' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-speedbar.png\" /></div>",
                'colspan' => 9,
            ),
            'score' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-scorebar.png\" /></div>",
                'colspan' => 9,
            ),
            'ranks' => array(
                'contents' => "<div class=\"center\"><img src=\"{$this->page->getImagesHttpPath()}/menu-ranksbar.png\" /></div>",
                'colspan' => 5,
            )
        ));
        
        $data_table->setHeader(array(
            'rank' => '&nbsp;',
            'personaname' => '&nbsp;',
            'cadence_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/cadence.png\" />",
            'bard_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/bard.png\" />",
            'monk_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/monk.png\" />",
            'aria_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/aria.png\" />",
            'bolt_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/bolt.png\" />",
            'dove_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/dove.png\" />",
            'eli_speed_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/eli.png\" />",
            'melody_speed_rank' => 'Melody',
            'dorian_speed_rank' => 'Dorian',
            'cadence_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/cadence.png\" />",
            'bard_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/bard.png\" />",
            'monk_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/monk.png\" />",
            'aria_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/aria.png\" />",
            'bolt_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/bolt.png\" />",
            'dove_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/dove.png\" />",
            'eli_score_rank' => "<img src=\"{$this->page->getImagesHttpPath()}/eli.png\" />",
            'melody_score_rank' => 'Melody',
            'dorian_score_rank' => 'Dorian',
            'speed_total' => 'Speed',
            'score_total' => 'Score',
            'base' => 'Base',
            'top_10_bonus' => 'Bonus',
            'weighted' => 'Weighted',
        ));
        
        //$data_table->setRowsPerPageOptions(array(25, 50, 100));
        
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