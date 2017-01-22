<?php
/**
* The base class of the API for Necrolab.
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
namespace Modules\Necrolab\Controllers\Api;

use \DateTime;
use \Framework\Core\Controllers\Web as WebController;

class Necrolab
extends WebController {       
    protected $date;
    
    protected $start;
    
    protected $limit;
    
    protected $sort_by;
    
    protected $sort_direction;
    
    public function __construct() {
        parent::__construct('necrolab');
    }
    
    protected function setDateFromRequest() {
        request()->setRequired(array(
            'date'
        ));
    
        $submitted_date = request()->date;
        
        if(!empty($submitted_date)) {
            $submitted_date_object = DateTime::createFromFormat('Y-m-d', $submitted_date);
        
            if(!empty($submitted_date_object)) {
                $this->date = new DateTime($submitted_date_object->format('Y-m-d'));
            }
        }
        
        if(empty($this->date)) {
            throw new Exception("date provided in request is invalid.");
        }
    }
    
    protected function getResultsetStateFromRequest() {
        $this->start = request()->get->getVariable('start', 'integer');
        
        if(empty($this->start)) {
            $this->start = 0;
        }
        
        $this->limit = request()->get->getVariable('limit', 'integer');
        
        if(empty($this->limit) || $this->limit < 0 || $this->limit > 10000) {
            $this->limit = 100;
        }
        
        $this->sort_by = request()->sort_by;
        
        $this->sort_direction = request()->sort_direction;
    }
 
    public function init() {
        $this->setDateFromRequest();
        
        $this->getResultsetStateFromRequest();
    }
    
    protected function getResultset() {}
    
    protected function getPlayerData($row) {
        return array(
            'steamid' => $row['steamid'],
            'personaname' => $row['personaname'],
            'twitch_username' => $row['twitch_username'],
            'twitter_username' => $row['twitter_username'],
            'nico_nico_url' => $row['nico_nico_url'],
            'hitbox_username' => $row['hitbox_username'],
            'website' => $row['website']
        );
    }
    
    public function formatResponse($data) {
        return $data;
    }
    
    public function actionGet() {        
        $resultset = $this->getResultset();
        
        $resultset->enableTotalRecordCount();
        
        $resultset->setOffset($this->start);
        
        if(!empty($this->limit)) {
            $resultset->setRowsPerPage($this->limit);
        }
        
        if(!empty($this->sort_by) && !empty($this->sort_direction)) {
            $resultset->setSortCriteriaFromAlias($this->sort_by, $this->sort_direction);
        }
        
        $resultset->addProcessorFunction(array(
            $this,
            'formatResponse'
        ));
        
        $resultset->process();
        
        $request = array(
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'start' => $this->start,
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
            'limit' => $this->limit
        );
        
        if(!empty($this->date)) {
            $request['date'] = $this->date->format('Y-m-d');
        }

        return array(
            'request' => $request,
            'data' => $resultset->getData()
        );
    }
}