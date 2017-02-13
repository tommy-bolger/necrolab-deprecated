<?php
/**
* The the api endpoint for leaderboards of Necrolab.
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
namespace Modules\Necrolab\Controllers\Api\Leaderboards;

use \Modules\Necrolab\Controllers\Api\Necrolab;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards as LeaderboardsModel;

class Leaderboards
extends Necrolab {
    protected $lbid;
    
    protected function setLbidFromRequest() {        
        $lbid = request()->get->lbid;
        
        if(empty($lbid)) {
            $this->framework->outputManualError(400, "Required property 'lbid' was not found in the request.");
        }
        
        $this->lbid = request()->get->getVariable('lbid', 'integer');
        
        if(empty($this->lbid)) {
            $this->framework->outputManualError(400, "Property '{$this->lbid}' is invalid. Please refer to /api/leaderboards for a list of valid lbids.");
        }
        
        $this->request['lbid'] = $this->lbid;
    }

    public function init() {
        $this->setReleaseFromRequest();
    
        $this->getResultsetStateFromRequest();
    }

    protected function getResultSet() {
        $resultset = LeaderboardsModel::getAllBaseResultset($this->release_name);
        
        return $resultset;
    }
    
    public function formatResponse($data) {        
        $processed_data = array();
        
        if(!empty($data)) {        
            foreach($data as $row) {
                $processed_data[] = LeaderboardsModel::getFormattedApiRecord($row);
            }
        }
        
        return $processed_data;
    }
}