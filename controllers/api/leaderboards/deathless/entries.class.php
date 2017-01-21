<?php
/**
* The deathless entries page of the leaderboards section of Necrolab.
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
namespace Modules\Necrolab\Controllers\Page\Leaderboards\Deathless;

use \Modules\Necrolab\Controllers\Page\Leaderboards\Entries as BaseEntries;
use \Modules\Necrolab\Models\Leaderboards\Replays;

class Entries
extends BaseEntries {
    protected function getTableHeader() {
        $header = parent::getTableHeader();
        
        $header['win_count'] = 'Wins';
        $header['highest_zone'] = 'Zone';
        $header['highest_level'] = 'Level';
        $header['is_win'] = "Win";
        $header['seed'] = 'Seed';
        $header['replay'] = 'Replay';
        
        return $header;
    }
    
    protected function getTableRow(array $row) {
        $table_row = parent::getTableRow($row);
        
        $table_row['win_count'] = $row['win_count'];
        $table_row['highest_zone'] = $row['zone'];
        $table_row['highest_level'] = $row['level'];
        $table_row['is_win'] = $row['is_win'];
        $table_row['seed'] = $row['seed'];
        
        $ugcid = $row['ugcid'];
        $replay = NULL;
        
        if(!empty($ugcid)) {
            $replay = $this->getReplayLink(Replays::getHttpFilePath($ugcid));
        }
        
        $table_row['replay'] = $replay;
        
        return $table_row;
    }
}