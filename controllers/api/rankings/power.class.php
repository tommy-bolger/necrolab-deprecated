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
namespace Modules\Necrolab\Controllers\Api\Rankings;

use \Modules\Necrolab\Controllers\Api\Necrolab;
use \Modules\Necrolab\Models\Characters\Database\Characters as CharactersModel;
use \Modules\Necrolab\Models\Rankings\Database\Power as PowerRankingsModel;

class Power
extends Necrolab {
    protected function getResultSet() {
        return PowerRankingsModel::getEntriesResultset($this->date);
    }
    
    public function formatResponse($data) {        
        $processed_data = array();
        
        if(!empty($data)) {
            $active_characters = CharactersModel::getActive();
        
            foreach($data as $row) {
                $processed_row['player'] = $this->getPlayerData($row);
            
                foreach($active_characters as $active_character) {
                    $name = $active_character['name'];
                    
                    $character_rankings = array(
                        'score_rank' => $row["{$name}_score_rank"],
                        'score_rank_points' => $row["{$name}_score_rank_points"],
                        'score' => $row["{$name}_score"],
                        'speed_rank' => $row["{$name}_speed_rank"],
                        'speed_rank_points' => $row["{$name}_speed_rank_points"],
                        'speed_time' => $row["{$name}_speed_time"]
                    );
                    
                    if(isset($row["{$name}_deathless_rank"])) {
                        $character_rankings['deathless_rank'] = $row["{$name}_deathless_rank"];
                        $character_rankings['deathless_rank_points'] = $row["{$name}_deathless_rank_points"];
                        $character_rankings['deathless_win_count'] = $row["{$name}_deathless_win_count"];
                    }
                    
                    $character_rankings['rank'] = $row["{$name}_rank"];
                    $character_rankings['rank_points'] = $row["{$name}_rank_points"];
                    
                    $processed_row[$name] = $character_rankings;
                }

                $score_rankings = array(
                    'rank' => $row['score_rank'],
                    'rank_points' => $row['score_rank_points_total']
                );
                
                $processed_row['score'] = $score_rankings;
                
                $speed_rankings = array(
                    'rank' => $row['speed_rank'],
                    'rank_points' => $row['speed_rank_points_total']
                );
                
                $processed_row['speed'] = $speed_rankings;
                
                $deathless_rankings = array(
                    'rank' => $row['deathless_rank'],
                    'rank_points' => $row['deathless_rank_points_total']
                );
                
                $processed_row['deathless'] = $deathless_rankings;
                
                $processed_row['rank'] = $row['rank'];
                $processed_row['total_points'] = $row['total_points'];
                
                $processed_data[] = $processed_row;
            }
        }
        
        return $processed_data;
    }
}