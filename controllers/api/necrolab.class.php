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
use \Modules\Necrolab\Models\Releases as ReleasesModel;
use \Modules\Necrolab\Models\Modes as ModesModel;
use \Modules\Necrolab\Models\Characters as CharactersModel;
use \Modules\Necrolab\Models\ExternalSites as ExternalSitesModel;
use \Modules\Necrolab\Models\Dailies\Rankings\DayTypes as DayTypesModel;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards;
use \Modules\Necrolab\Models\SteamUsers\CacheNames as SteamUsersCacheNames;

class Necrolab
extends WebController {
    protected $cached_response_prefix_name;
    
    protected $cached_response_store_time = 60;

    protected $request = array();

    protected $release_name;
    
    protected $release_id;
    
    protected $mode;
    
    protected $mode_id;

    protected $date;
    
    protected $number_of_days;
    
    protected $daily_ranking_day_type_id;
    
    protected $site;
    
    protected $external_site_id;
    
    protected $character_name;
    
    protected $character_id;
    
    protected $start_date;
    
    protected $end_date;
    
    protected $lbid;
    
    protected $leaderboard_id;
    
    protected $seeded;
    
    protected $co_op;
    
    protected $custom;
    
    protected $ugcid;
    
    protected $steamid;
    
    protected $start;
    
    protected $limit;
    
    protected $search;
    
    protected $enable_search = false;
    
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
        
        $this->release_id = $release_record['release_id'];
        
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
        
        $this->mode_id = $mode_record['mode_id'];
        
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
        
        if(empty($this->limit)) {
            $this->limit = 100;
        }
        
        $this->search = request()->search;
        
        $this->request['start'] = $this->start;
        $this->request['limit'] = $this->limit;
        
        if(strlen($this->search) > 0) {
            $this->request['search'] = $this->search;
        }
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
        
        $this->daily_ranking_day_type_id = $active_day_types[$this->number_of_days]['daily_ranking_day_type_id'];
        
        $this->request['number_of_days'] = $this->number_of_days;
    }
    
    protected function setSiteFromRequest() {        
        $this->site = request()->get->site;
        
        if(!empty($this->site)) {
            $external_site_record = ExternalSitesModel::getActiveByName($this->site);
            
            if(empty($external_site_record)) {
                $this->framework->outputManualError(400, "Specified site '{$this->site}' is invalid. Please refer to the /api/external_sites endpoint for a list of valid sites.");
            }
            
            $this->external_site_id = $external_site_record['external_site_id'];
        }
        
        if(empty($this->external_site_id)) {
            $this->external_site_id = 0;
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
        
        $this->character_id = $character_record['character_id'];
        
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
    
    protected function setLbidFromRequest() {        
        $lbid = request()->get->lbid;
        
        if(empty($lbid)) {
            $this->framework->outputManualError(400, "Required property 'lbid' was not found in the request.");
        }
        
        $this->lbid = request()->get->getVariable('lbid', 'integer');
        
        $this->leaderboard_id = Leaderboards::getId($this->lbid);
        
        if(empty($this->lbid)) {
            $this->framework->outputManualError(400, "Property '{$this->lbid}' is invalid. Please refer to /api/leaderboards for a list of valid lbids.");
        }
        
        $this->request['lbid'] = $this->lbid;
    }
    
    protected function setSeededFromRequest() {        
        $seeded = request()->get->seeded;
        
        if(strlen($seeded) == 0) {
            $this->framework->outputManualError(400, "Required property 'seeded' was not found in the request.");
        }
        
        $this->seeded = request()->get->getVariable('seeded', 'integer');
        
        if($this->seeded != 0 && $this->seeded != 1) {
            $this->framework->outputManualError(400, "Specified seeded value of '{$this->seeded}' is invalid. It can only be either 0 or 1.");
        }
        
        $this->request['seeded'] = $this->seeded;
    }
    
    protected function setCoOpFromRequest() {        
        $co_op = request()->get->co_op;
        
        if(strlen($co_op) == 0) {
            $this->framework->outputManualError(400, "Required property 'co-op' was not found in the request.");
        }
        
        $this->co_op = request()->get->getVariable('co_op', 'integer');
        
        if($this->co_op != 0 && $this->co_op != 1) {
            $this->framework->outputManualError(400, "Specified co_op value of '{$this->co_op}' is invalid. It can only be either 0 or 1.");
        }
        
        $this->request['co_op'] = $this->co_op;
    }
    
    protected function setCustomFromRequest() {        
        $custom = request()->get->custom_music;
        
        if(strlen($custom) == 0) {
            $this->framework->outputManualError(400, "Required property 'custom_music' was not found in the request.");
        }
        
        $this->custom = request()->get->getVariable('custom_music', 'integer');
        
        if($this->custom != 0 && $this->custom != 1) {
            $this->framework->outputManualError(400, "Specified custom_music value of '{$this->custom}' is invalid. It can only be either 0 or 1.");
        }
        
        $this->request['custom_music'] = $this->custom;
    }
    
    protected function setUgcidFromRequest() {        
        $ugcid = request()->get->ugcid;
        
        if(empty($ugcid)) {
            $this->framework->outputManualError(400, "Required property 'ugcid' was not found in the request.");
        }
        
        $this->ugcid = $ugcid;
        
        $this->request['ugcid'] = $this->ugcid;
    }
    
    protected function setSteamidFromRequest() {  
        $steamid = request()->get->steamid;
    
        if(empty($steamid)) {
            $this->framework->outputManualError(400, "Required property 'steamid' was not found in the request.");
        }
    
        $steamid = request()->get->getVariable('steamid', 'integer');
        
        if(empty($steamid)) {
            $this->framework->outputManualError(400, "Required property 'steamid' is not a valid 64-bit integer.");
        }
        
        $this->steamid = request()->get->steamid;
        
        $this->request['steamid'] = $this->steamid;
    }
 
    public function init() {        
        $this->setReleaseFromRequest();
    
        $this->setDateFromRequest();
        
        $this->getResultsetStateFromRequest();
    }
    
    protected function getResultset() {}
    
    protected function getPlayerData($row) {        
        $steamid = (string)$row['steamid'];
        
        $discord_discriminator = NULL;
        
        if(!empty($row['discord_discriminator'])) {
            $discord_discriminator = str_pad($row['discord_discriminator'], 4, '0', STR_PAD_LEFT);
        }
    
        return array(
            'steamid' => $steamid,
            'personaname' => $row['steam_personaname'],
            'linked' => array(
                'steam' => array(
                    'id' => $steamid,
                    'personaname' => $row['steam_personaname'],
                    'profile_url' => $row['steam_profile_url']
                ),
                'twitch' => array(
                    'id' => $row['twitch_id'],
                    'username' => $row['twitch_username']
                ),
                'discord' => array(
                    'id' => $row['discord_id'],
                    'username' => $row['discord_username'],
                    'discriminator' => $discord_discriminator
                ),
                'reddit' => array(
                    'id' => $row['reddit_id'],
                    'username' => $row['reddit_username']
                ),
                'youtube' => array(
                    'id' => $row['youtube_username'],
                    'username' => $row['youtube_username']
                ),
                'twitter' => array(
                    'id' => $row['twitter_id'],
                    'nickname' => $row['twitter_nickname'],
                    'name' => $row['twitter_name']
                ),
                'beampro' => array(
                    'id' => $row['beampro_id'],
                    'username' => $row['beampro_username']
                )
            )
        );
    
        return $player_data;
    }
    
    public function formatResponse($data) {
        return $data;
    }
    
    public function actionGet() {
        $cache = cache('local');
        
        $cached_response = array();
        
        $cached_response_unique_name = md5(implode('_', $this->request));
    
        if(isset($this->cached_response_prefix_name)) {  
            $cached_response = $cache->get($cached_response_unique_name, $this->cached_response_prefix_name);
        }
        
        $response_data_count = 0;
        $response_data = array();
    
        if(!empty($cached_response)) {
            $response_data_count = $cached_response['count'];
            $response_data = $cached_response['data'];
        }
        else {
            $resultset = $this->getResultset();

            if(isset($this->start) && isset($this->limit)) {
                $resultset->enableTotalRecordCount();
                
                $resultset->setOffset($this->start);
                
                $resultset->setRowsPerPage($this->limit);
            }
            
            if(!empty($this->enable_search) && !empty($this->search)) {                
                $resultset->setSearch(SteamUsersCacheNames::getUsersByName(), $this->search);
            }
            
            $resultset->addProcessorFunction(array(
                $this,
                'formatResponse'
            ));
            
            $resultset->process();
            
            $response_data_count = $resultset->getTotalNumberOfRecords();
            $response_data = $resultset->getData();
            
            if(isset($this->cached_response_prefix_name)) {        
                $cache->set($cached_response_unique_name, array(
                    'data' => $response_data,
                    'count' => $response_data_count
                ), $this->cached_response_prefix_name, $this->cached_response_store_time);
            }
        }

        return $this->getResponse($response_data_count, $response_data);
    }
    
    protected function getResponse($record_count, $data) {
        return array(
            'request' => $this->request,
            'record_count' => $record_count,
            'data' => $data
        );
    }
}