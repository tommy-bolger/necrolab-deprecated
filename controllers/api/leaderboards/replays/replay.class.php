<?php
/**
* The api endpoint for a single replay in Necrolab.
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
namespace Modules\Necrolab\Controllers\Api\Leaderboards\Replays;

use \Modules\Necrolab\Models\Leaderboards\Database\Replays as ReplaysModel;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards as LeaderboardsModel;
use \Modules\Necrolab\Models\SteamUsers\Database\Pbs as SteamUserPbsModel;

class Replay
extends Replays {
    public function init() {
        $this->setUgcidFromRequest();
    }

    protected function getResultSet() {
        return ReplaysModel::getOneResultset($this->ugcid);
    }
    
    public function formatResponse($data) {        
        $processed_data = array();
        
        if(!empty($data) && !empty($data[0])) {
            $row = $data[0];
        
            $processed_data = array(
                'steamid' => $row['steamid'],
                'leaderboard' => LeaderboardsModel::getFormattedApiRecord($row),
                'pb' => SteamUserPbsModel::getFormattedApiRecord($row),
                'replay' => ReplaysModel::getFormattedApiRecord($row),
            );
        }
        
        return $processed_data;
    }
}