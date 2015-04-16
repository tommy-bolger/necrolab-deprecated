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
use \Framework\Modules\ModulePage;

class Necrolab
extends Controller {   
    protected $title;

    protected $active_page;
 
    public function __construct() {
        parent::__construct();
    }

    protected function loadModule() {
        $this->page = new ModulePage('necrolab', 'necrolab_home');
        
        $this->page->setTitle("NecroLab::{$this->title}");

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

        if($this->active_page == 'power_rankings') {
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
    
    public function updateTableState() {
        $data_table = $this->getDataTable();
        
        return $data_table->toJsonArray();
    }
}