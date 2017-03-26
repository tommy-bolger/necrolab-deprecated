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

use \Exception;
use \DateTime;
use \Framework\Core\Controllers\Web as WebController;
use \Modules\Necrolab\Models\Releases\Database\Releases as ReleasesModel;
use \Modules\Necrolab\Models\Modes\Database\Modes as ModesModel;
use \Modules\Necrolab\Models\Characters\Database\Characters as CharactersModel;
use \Modules\Necrolab\Models\ExternalSites\Database\ExternalSites as ExternalSitesModel;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\DayTypes as DayTypesModel;

class Necrolab
extends WebController {
    protected $request = array();

    protected $release_name;
    
    protected $mode;

    protected $date;
    
    protected $number_of_days;
    
    protected $site;
    
    protected $character_name;
    
    protected $start_date;
    
    protected $end_date;
    
    protected $start;
    
    protected $limit = 100;
    
    protected $sort_by;
    
    protected $sort_direction;
    
    protected $search;
    
    public function __construct() {
        parent::__construct('necrolab');
    }
    
    protected function setReleaseFromRequest() {        
        $this->release_name = request()->get->release;
        
        if(empty($this->release_name)) {
            $this->framework->outputManualError(400, "Required property 'release' was not found in the request.");
        }
        
        $release_record = ReleasesModel::getByName($this->release_name);
        
        if(empty($release_record)) {
            $this->framework->outputManualError(400, "Specified release '{$this->release_name}' is invalid. Please refer to the /api/releases endpoint for a list of valid releases.");
        }
        
        $this->request['release'] = $this->release_name;
    }
    
    protected function setModeFromRequest() {
        $mode = request()->get->mode;

        if(strlen($mode) == 0) {
            $this->framework->outputManualError(400, "Required property 'mode' was not found in the request.");
        }
    
        $this->mode = $mode;
        
        $mode_record = ModesModel::getByName($this->mode);
        
        if(empty($mode_record)) {
            $this->framework->outputManualError(400, "Specified property 'mode' is not a valid mode. Please refer to /api/modes for a list of valid values.");
        }
        
        $this->request['mode'] = $this->mode;
    }
    
    protected function setDateFromRequest() {    
        $submitted_date = request()->date;
        
        if(empty($submitted_date)) {
            $this->framework->outputManualError(400, "Required property 'date' was not found in the request.");
        }
        
        if(!empty($submitted_date)) {
            $submitted_date_object = DateTime::createFromFormat('Y-m-d', $submitted_date);
        
            if(!empty($submitted_date_object)) {
                $this->date = new DateTime($submitted_date_object->format('Y-m-d'));
            }
        }
        
        if(empty($this->date)) {
            $this->framework->outputManualError(400, "Required property 'date' is invalid. All dates must be specified in YYYY-MM-DD format.");
        }
        
        $this->request['date'] = $this->date->format('Y-m-d');
    }
    
    protected function getResultsetStateFromRequest() {
        $this->start = request()->get->getVariable('start', 'integer');
        
        if(empty($this->start)) {
            $this->start = 0;
        }
        
        $this->limit = request()->get->getVariable('limit', 'integer');
        
        if(empty($this->limit) || $this->limit < 0 || $this->limit > 1000) {
            $this->limit = 100;
        }
        
        $this->sort_by = request()->sort_by;
        
        $this->sort_direction = request()->sort_direction;
        
        $this->search = request()->search;
        
        $this->request['start'] = $this->start;
        $this->request['limit'] = $this->limit;
        $this->request['sort_by'] = $this->sort_by;
        $this->request['sort_direction'] = $this->sort_direction;
    }
    
    protected function setNumberOfDaysFromRequest() {
        $number_of_days = request()->get->number_of_days;

        if(strlen($number_of_days) == 0) {
            $this->framework->outputManualError(400, "Required property 'number_of_days' was not found in the request.");
        }
    
        $this->number_of_days = request()->get->getVariable('number_of_days', 'integer');
        
        if(!isset($this->number_of_days)) {
            $this->framework->outputManualError(400, "Specified property 'number_of_days' is not a valid integer. Please refer to /api/rankings/daily/number_of_days for a list of valid values.");
        }
        
        $active_day_types = DayTypesModel::getActive();
        
        if(empty($active_day_types[$this->number_of_days])) {
            $this->framework->outputManualError(400, "Specified property 'number_of_days' is not valid. Please refer to /api/rankings/daily/number_of_days for a list of valid values.");
        }
        
        $this->request['number_of_days'] = $this->number_of_days;
    }
    
    protected function setSiteFromRequest() {        
        $this->site = request()->get->site;
        
        if(!empty($this->site)) {
            $external_site_record = ExternalSitesModel::getActiveByName($this->site);
            
            if(empty($external_site_record)) {
                $this->framework->outputManualError(400, "Specified site '{$this->site}' is invalid. Please refer to the /api/external_sites endpoint for a list of valid sites.");
            }
        }
        
        $this->request['site'] = $this->site;
    }
    
    protected function setCharacterFromRequest() {
        $character_name = request()->get->character;

        if(strlen($character_name) == 0) {
            $this->framework->outputManualError(400, "Required property 'character' was not found in the request.");
        }
    
        $this->character_name = $character_name;
        
        $character_record = CharactersModel::getActiveByName($this->character_name);
        
        if(empty($character_record)) {
            $this->framework->outputManualError(400, "Specified property 'character' is not a valid character name. Please refer to /api/characters for a list of valid values.");
        }
        
        $this->request['character'] = $this->character_name;
    }
    
    protected function setDateRangeFromRequest() {    
        $start_date = request()->get->start_date;
        
        if(empty($start_date)) {
            $this->framework->outputManualError(400, "Required property 'start_date' was not found in the request.");
        }
        
        if(!empty($start_date)) {
            $start_date_object = DateTime::createFromFormat('Y-m-d', $start_date);
        
            if(!empty($start_date_object)) {
                $this->start_date = new DateTime($start_date_object->format('Y-m-d'));
            }
        }
        
        if(empty($this->start_date)) {
            $this->framework->outputManualError(400, "Property 'start_date' is not a valid date. Please specify a date in the YYYY-MM-DD format.");
        }
        
        $end_date = request()->get->end_date;
        
        if(!empty($end_date)) {
            $end_date_object = DateTime::createFromFormat('Y-m-d', $end_date);
        
            if(!empty($end_date_object)) {
                $this->end_date = new DateTime($end_date_object->format('Y-m-d'));
            }
        }
        
        if(empty($this->end_date)) {
            $this->end_date = new DateTime(date('Y-m-d'));
        }
        
        $this->request['start_date'] = $this->start_date->format('Y-m-d');
        $this->request['end_date'] = $this->end_date->format('Y-m-d');
    }
 
    public function init() {        
        $this->setReleaseFromRequest();
    
        $this->setDateFromRequest();
        
        $this->getResultsetStateFromRequest();
    }
    
    protected function getResultset() {}
    
    protected function getPlayerData($row) {
        return array(
            'steamid' => (string)$row['steamid'],
            'personaname' => $row['personaname'],
            'linked' => array(
                'steam' => array(
                    'personaname' => $row['steam_personaname'],
                    'profile_url' => $row['steam_profile_url']
                ),
                'twitch' => $row['twitch_username'],
                'discord' => array(
                    'username' => $row['discord_username'],
                    'discriminator' => $row['discord_discriminator']
                ),
                'reddit' => $row['reddit_username'],
                'youtube' => $row['youtube_username'],
                'twitter' => array(
                    'nickname' => $row['twitter_nickname'],
                    'name' => $row['twitter_name']
                ),
                'beampro' => $row['beampro_username'],
            )
        );
    }
    
    public function formatResponse($data) {
        return $data;
    }
    
    public function actionGet() {        
        $resultset = $this->getResultset();
        
        if(!empty($this->site)) {
            ExternalSitesModel::addSiteUserJoin($resultset, $this->site);
        }
        
        $resultset->enableTotalRecordCount();
        
        $resultset->setOffset($this->start);
        
        $resultset->setRowsPerPage($this->limit);
        
        if(!empty($this->sort_by) && !empty($this->sort_direction)) {
            $resultset->setSortCriteriaFromAlias($this->sort_by, $this->sort_direction);
        }
        
        if(!empty($this->search)) {
            $search_select_field = $resultset->getSelectField('personaname');
            
            if(!empty($search_select_field)) {
                $resultset->addFilterCriteria("{$search_select_field} ILIKE :search", array(
                    ':search' => "%{$this->search}%"
                ));
            }
        }
        
        $resultset->addProcessorFunction(array(
            $this,
            'formatResponse'
        ));
        
        $resultset->process();

        return array(
            'request' => $this->request,
            'record_count' => $resultset->getTotalNumberOfRecords(),
            'data' => $resultset->getData()
        );
    }
}