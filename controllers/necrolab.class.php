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
        
        $this->page->body->addChild($this->active_page, 'active_page');

        $this->constructContent();
    }
    
    public function addSocialMediaToTable($result_data) {
        if(!empty($result_data)) {                                    
            foreach($result_data as &$row) {                                                    
                $personaname = $row['personaname'];
            
                $row['personaname'] = "<a href=\"/player?steam_user_id={$row['steam_user_id']}\">{$personaname}</a>";
                
                $social_media = '';

                if(!empty($row['twitch_username'])) {
                    $social_media .= "<a href=\"http://www.twitch.tv/{$row['twitch_username']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/twitch_small.png\" alt=\"Twitch Channel for {$personaname}\" /></a>";
                }
                
                if(!empty($row['twitter_username'])) {
                    $social_media .= "<a href=\"http://www.twitter.com/{$row['twitter_username']}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/twitter_logo_blue_small.png\" alt=\"Twitter Feed for {$personaname}\" /></a>";
                }
                
                if(!empty($row['website'])) {
                    $website_url = $row['website'];
                    
                    if(strpos($website_url, 'http://') === false && strpos($website_url, 'https://') === false) {
                        $website_url = "http://{$website_url}";
                    }
                
                    $social_media .= "<a href=\"{$website_url}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/external_link_small.png\" alt=\"Website of {$personaname}\" /></a>";
                }
                
                if(empty($social_media)) {
                    $social_media = '&nbsp';
                }
                
                $row_first_part = array_slice($row, 0, 1, true);
                $row_second_part = array_slice($row, 1, (count($row) - 1), true);
                
                $row_first_part['social_media'] = $social_media;
                
                $row = array_merge($row_first_part, $row_second_part);
            }
        }
        
        return $result_data;
    }
    
    public function updateTableState() {
        $data_table = $this->getDataTable();
        
        return $data_table->toJsonArray();
    }
}