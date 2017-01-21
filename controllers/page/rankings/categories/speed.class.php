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

use \Framework\Html\Table\DataTable;
use \Framework\Utilities\Http;
use \Modules\Necrolab\Models\Rankings\Database\Speed as SpeedRankingsModel;

class Speed
extends Categories {   
    protected $title = 'Speed Rankings';
 
    public function init() {
        parent::init();
        
        $this->active_page = 'speed_rankings';
    }
    
    public function action() {    
        $this->page->body->addChild($this->getDataTable(), 'content');
    }
    
    protected function getDataTable() {    
        $resultset = SpeedRankingsModel::getEntriesDisplayResultset($this->date);  
        
        $data_table = new DataTable("speed_rankings", false);
        
        $data_table->disableJavascript();
        
        $data_table->addRequestVariable('date', $this->date->format('Y-m-d'));
        
        $data_table->setNumberofColumns(16);
        
        $data_table->setHeader($this->getTableHeader('speed'));
        
        $filter_textbox = $data_table->addFilterTextbox('personaname', '*?*', NULL);
        
        $filter_textbox->setAttribute('placeholder', 'Search Players');
        
        $data_table->process($resultset, function($result_data) {
            return $this->processTableData('speed', $result_data);
        });
        
        return $data_table;
    }
    
    /*public function apiLatestRankings() {
        $page_number = request()->get->getVariable('page', 'integer');
        
        if(empty($page_number)) {
            $page_number = 1;
        }
        
        $resultset = SpeedRankingsModel::getLatestRankingsFromCache($page_number, 100);
        
        $resultset->process();

        return array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'pages' => $resultset->getTotalPages(),
            'current_page' => $page_number,
            'data' => $resultset->getData()
        );
    }*/
}