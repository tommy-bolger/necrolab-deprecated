<?php
/**
* The api endpoint for a player's achievements in Necrolab.
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
namespace Modules\Necrolab\Controllers\Api\Players\Player;

use \Modules\Necrolab\Models\Achievements\Database\Achievements as AchievementsModel;
use \Modules\Necrolab\Models\SteamUsers\Database\Steamusers as SteamUsersModel;
use \Modules\Necrolab\Models\SteamUsers\Database\Achievements as SteamUserAchievementsModel;

class Achievements
extends Player {    
    public function init() {
        $this->setSteamidFromRequest();
    }
    
    public function actionGet() {
        $achievements = AchievementsModel::getAll();
        
        $steam_user = SteamUsersModel::get($this->steamid);
        
        $full_achievements = array();
        
        if(!empty($steam_user)) {
            $steam_user_id = $steam_user['steam_user_id'];
        
            $player_achievements = SteamUserAchievementsModel::getForUser($steam_user_id);
            
            if(!empty($achievements)) {
                foreach($achievements as $achievement) {
                    $achievement_id = $achievement['achievement_id'];
                    
                    $full_achievement = array(
                        'name' => $achievement['name'],
                        'display_name' => $achievement['display_name'],
                        'description' => $achievement['description']
                    );
                    
                    $achivement_record = array();
                
                    if(!empty($player_achievements)) {
                        foreach($player_achievements as $player_achievement) {
                            $player_achievement_id = $player_achievement['achievement_id'];
                        
                            if($player_achievement_id == $achievement_id) {
                                $achivement_record = $player_achievement;
                                
                                break;
                            }
                        }
                    }
                    
                    if(!empty($achivement_record)) {
                        $full_achievement['achieved'] = 1;
                        $full_achievement['achieved_date'] = $achivement_record['achieved'];
                        $full_achievement['icon_url'] = $achievement['icon_url'];
                    }
                    else {
                        $full_achievement['achieved'] = 0;
                        $full_achievement['achieved_date'] = NULL;
                        $full_achievement['icon_url'] = $achievement['icon_gray_url'];
                    }
                    
                    $full_achievements[] = $full_achievement;
                }
            }
        }
        
        return array(
            'request' => array(
                'steamid' => $this->steamid
            ),
            'record_count' => count($achievements),
            'data' => $full_achievements
        );
    }
}