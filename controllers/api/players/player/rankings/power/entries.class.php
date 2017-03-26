<?php
/**
* The api endpoint for a player's power ranking entries in Necrolab.
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
namespace Modules\Necrolab\Controllers\Api\Players\Player\Rankings\Power;

use \Exception;
use \Modules\Necrolab\Models\Characters\Database\Characters as CharactersModel;
use \Modules\Necrolab\Models\Rankings\Database\Entries as PowerRankingEntriesModel;
use \Modules\Necrolab\Models\Rankings\Database\Entry as PowerRankingEntryModel;
use \Modules\Necrolab\Models\Modes\Database\Modes as ModesModel;

class Entries
extends Power {    
    public function init() {
        $this->setSteamidFromRequest();
    
        $this->setReleaseFromRequest();
        
        $this->setDateRangeFromRequest();
    
        $this->getResultsetStateFromRequest();
    }

    protected function getResultSet() {
        return PowerRankingEntriesModel::getSteamUserBaseResultset($this->release_name, $this->steamid, $this->start_date, $this->end_date);
    }
    
    public function formatResponse($data) {        
        $processed_data = array();
        
        if(!empty($data)) {
            $active_characters = CharactersModel::getActive();
        
            foreach($data as $row) {
                $processed_row = array();
                
                $processed_row['date'] = $row['date'];
                
                $processed_row['mode'] = ModesModel::getFormattedApiRecord($row);
                
                $processed_row['steamid'] = $row['steamid'];
            
                $processed_row = array_merge($processed_row, PowerRankingEntryModel::getFormattedApiRecord($active_characters, $row));
                
                $processed_data[] = $processed_row;
            }
        }
        
        return $processed_data;
    }
}