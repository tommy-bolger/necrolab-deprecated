<?php
/**
* The home page of Necrolab.
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
namespace Modules\Necrolab\Controllers\Page;

use \DateTime;
use \Framework\Core\Controllers\Page as PageController;
use \Framework\Modules\ModulePage;
use \Framework\Display\Template;
use \Framework\Html\Misc\TemplateElement;

class Necrolab
extends PageController {   
    protected $title;
    
    protected $has_table_title = false;
    
    protected $active_page_category;

    protected $active_page;
    
    protected $date;
    
    protected $submitted_date;
 
    public function __construct() {
        parent::__construct('necrolab');
        
        $submitted_date = request()->date;
        
        if(!empty($submitted_date)) {
            $submitted_date_object = DateTime::createFromFormat('Y-m-d', $submitted_date);
        
            if(!empty($submitted_date_object)) {
                $this->date = new DateTime($submitted_date_object->format('Y-m-d'));
            }
        }
        
        if(empty($this->date)) {
            $this->date = new DateTime(date('Y-m-d'));
        }
    }
    
    protected function addDataTableFiles() {        
        $this->page->addJavascriptFiles(array(
            'jquery.min.js',
            'datatables.min.js',
            'url.js',
            'formatting.js',
            'bootstrap-datepicker.min.js',
            'moment.min.js',
            'necrotable.js',
            'request.js'
        ));
    }
    
    public function setup() {        
        $this->page = new ModulePage('necrolab', 'necrolab_home');
        
        $this->page->setTitle("NecroLab::{$this->title}");

        $this->page->addCssFiles(array(
            'reset.css',
            'bootstrap.css',
            'main.css',
            '/jquery.dataTables.css',
            '/bootstrap-datepicker3.min.css',
            '/datepicker.css'
        ));
        
        header('Content-Type: text/html; charset=ISO-8859-1');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Access-Control-Allow-Origin: *', false);
        
        $this->page->setTemplate('home.php');
    
        $this->page->body->addChild("{$this->page->getImagesHttpPath()}/logotemp.png", 'site_logo');
        
        $this->page->body->addChild($this->active_page_category, 'active_page_category');
        $this->page->body->addChild($this->active_page, 'active_page');
        
        $last_refreshed_date = new DateTime();
        
        $current_time = new DateTime();
        $time_difference = $current_time->diff($last_refreshed_date);
        
        $this->page->body->addChild("Last updated {$time_difference->format('%i')} minutes and {$time_difference->format('%s')} seconds ago.", 'last_refreshed');
    }
    
    public function actionGet() {            
        $entries_table = new TemplateElement('entries_table.php');
        
        $entries_table->addChild($this->has_table_title, 'has_table_title');
        
        $this->page->body->addChild($entries_table, 'content');
    }
    
    protected function getCharacterImagePlaceholderUrl() {
        return "{$this->page->getImagesHttpPath()}/character_placeholder.png";
    }
    
    protected function getSocialMedia($row) { 
        $social_media_html = new Template('social_media.php');
    
        $social_media_html->setPlaceholderValues($row);
        
        return $social_media_html->parseTemplate();
    }
}