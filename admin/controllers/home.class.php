<?php
/**
* The home page of the NMecrolab section of the Admin module.
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

namespace Modules\Necrolab\Admin\Controllers;

use \Modules\Admin\Controllers\Home as AdminHome;
use \Framework\Modules\AdminPage;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;

class Home
extends AdminHome {
    protected $title = "Necrolab Admin Home";
    
    protected $active_top_link = 'Necrolab';

    public function __construct() {
        $this->loadManagedModule('necrolab');
    
        parent::__construct();
    }
    
    protected function loadModulePage() {
        $this->page = new AdminPage('necrolab');
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $page_url = Http::getInternalUrl('necrolab', array('admin'));
        
        $this->page_links['Necrolab Admin Home'] = $page_url;
        
        session()->module_path = $this->page_links;
    }
    
    protected function initializeModuleLinks() {
        $leaderboards_path = array(
            'admin',
            'leaderboards'
        );
            
        $blacklist_path = array(
            'admin',
            'leaderboards',
            'blacklist'
        );
        
        $daily_rankings_path = array(
            'admin',
            'daily_rankings'
        );
        
        $daily_ranking_seasons_path = array(
            'admin',
            'daily_rankings',
            'seasons'
        );
        
        if(isset(session()->necrolab_links)) {
            $this->module_links = session()->necrolab_links;
        }
        else {
            $this->module_links = array(
                'necrolab' => array(
                    'top_nav' => array(
                        'Necrolab' => Http::getInternalUrl('necrolab', array('admin'))
                    )
                ),
                'leaderboards' => array(
                    'top_nav' => array(
                        'Leaderboards' => Http::getInternalUrl('necrolab', $leaderboards_path, 'home')
                    ),
                    'sub_nav' => array(
                        'Blacklist' => array(
                            'Manage' => Http::getInternalUrl('necrolab', $blacklist_path, 'manage'),
                            'Add/Edit' => Http::getInternalUrl('necrolab', $blacklist_path, 'add')
                        )
                    )
                ),
                'daily_rankings' => array(
                    'top_nav' => array(
                        'Daily Rankings' => Http::getInternalUrl('necrolab', $daily_rankings_path, 'home')
                    ),
                    'sub_nav' => array(
                        'Seasons' => array(
                            'Manage' => Http::getInternalUrl('necrolab', $daily_ranking_seasons_path, 'manage'),
                            'Add/Edit' => Http::getInternalUrl('necrolab', $daily_ranking_seasons_path, 'add')
                        )
                    )
                ),
            );
            
            $this->module_links += $this->getErrorsLinks();
            
            $this->module_links += $this->getSettingsLinks();
        }
    }
    
    protected function constructRightContent() {
        $current_menu_content = new TemplateElement('home.php');
    
        $this->page->body->addChild($current_menu_content, 'current_menu_content');
    }
}