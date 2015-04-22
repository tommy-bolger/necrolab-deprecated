<?php
/**
* Retrieves steam user data for Crypt of the Necrodancer.
* Copyright (c) 2015, Tommy Bolger
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

use \Framework\Core\Modes\Cli\Framework;
use \Framework\Modules\Module;

require_once(dirname(dirname(dirname(__DIR__))) . '/framework/core/modes/cli/framework.class.php');

function display_help() {
    die(
        "\n================================================================================\n" . 
        "\nThis script retrieves steam users found in leaderboard entries.\n" . 
        "\nOptions:\n" . 
        "\n-h Displays this help." . 
        "\n================================================================================\n"
    );
}

$framework = new Framework('vh', false);

if(isset($framework->arguments->h)) {
    display_help();
}

$verbose_output = false;

if(isset($framework->arguments->v)) {
    $verbose_output = true;
}

$module = new Module('necrolab');

if($verbose_output) {
    $framework->cout("Getting saved steam users.\n");
}

$steam_users = db()->getMappedColumn("
    SELECT
        steamid,
        steam_user_id
    FROM steam_users 
    WHERE (
        updated IS NULL
        OR (updated + INTERVAL '30 day') < CURRENT_TIMESTAMP
    )
");

$steam_users_groups = array();

if(!empty($steam_users)) {
    if($verbose_output) {
        $framework->cout("Steam users found, grouping into sets of 100.\n");
    }

    $group_number = 1;
    $group_entry_number = 1;
    
    if($verbose_output) {
        $framework->cout("Creating group {$group_number}.\n");
    }

    foreach($steam_users as $steamid => $steam_user_id) {
        if($group_entry_number > 100) {
            $group_number += 1;
        
            $group_entry_number = 1;
        }
        
        $steam_users_groups[$group_number][$group_entry_number] = $steamid;
        
        $group_entry_number += 1;
    }
}
else {
    if($verbose_output) {
        $framework->cout("No steam users have been saved.\n");
    }
}

if(!empty($steam_users_groups)) {
    if($verbose_output) {
        $framework->cout("Processing steam user groups.\n");
    }
    
    $cache = cache();
    
    $request_context_options = array('http' =>
        array(
            'timeout' => 180
        )
    );
    
    $request_context = stream_context_create($request_context_options);

    foreach($steam_users_groups as $group_number => $steam_users_group) {
        if($verbose_output) {
            $framework->cout("Retrieving user data for group {$group_number}.\n");
        }
    
        $request_steam_ids = implode(',', $steam_users_group);
        
        $steam_users_json = file_get_contents(
            "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$module->configuration->steam_api_key}&steamids={$request_steam_ids}", 
            false,
            $request_context
        );
        
        $steam_users_data = json_decode($steam_users_json);
    
        if(!empty($steam_users_data->response->players)) {
            foreach($steam_users_data->response->players as $steam_user_data) {
                $steam_user_id = $steam_users[$steam_user_data->steamid];
                
                if($verbose_output) {
                    $framework->cout("Updating user {$steam_user_data->steamid}.\n");
                }
                
                $community_visibility_state = NULL;
                
                if(!empty($steam_user_data->communityvisibilitystate)) {
                    $community_visibility_state = $steam_user_data->communityvisibilitystate;
                }
                
                $profile_state = NULL;
                
                if(!empty($steam_user_data->profilestate)) {
                    $profile_state = $steam_user_data->profilestate;
                }
                
                $persona_name = NULL;
                
                if(!empty($steam_user_data->personaname)) {
                    $persona_name = $steam_user_data->personaname;
                }
                
                $last_logoff = NULL;
                
                if(!empty($steam_user_data->lastlogoff)) {
                    $last_logoff = $steam_user_data->lastlogoff;
                }
                
                $profile_url = NULL;
                
                if(!empty($steam_user_data->profileurl)) {
                    $profile_url = $steam_user_data->profileurl;
                }
                
                $avatar = NULL;
                
                if(!empty($steam_user_data->avatar)) {
                    $avatar = $steam_user_data->avatar;
                }
                
                $avatar_medium = NULL;
                
                if(!empty($steam_user_data->avatarmedium)) {
                    $avatar_medium = $steam_user_data->avatarmedium;
                }
                
                $avatar_full = NULL;
                
                if(!empty($steam_user_data->avatarfull)) {
                    $avatar_full = $steam_user_data->avatarfull;
                }
                
                $persona_state = NULL;
                
                if(!empty($steam_user_data->personastate)) {
                    $persona_state = $steam_user_data->personastate;
                }
                
                $real_name = NULL;
                
                if(!empty($steam_user_data->realname)) {
                    $real_name = $steam_user_data->realname;
                }
                
                $primary_clan_id = NULL;
                
                if(!empty($steam_user_data->primaryclanid)) {
                    $primary_clan_id = $steam_user_data->primaryclanid;
                }
                
                $time_created = NULL;
                
                if(!empty($steam_user_data->timecreated)) {
                    $time_created = $steam_user_data->timecreated;
                }
                
                $persona_state_flags = NULL;
                
                if(!empty($steam_user_data->personastateflags)) {
                    $persona_state_flags = $steam_user_data->personastateflags;
                }
                
                $loc_country_code = NULL;
                
                if(!empty($steam_user_data->loccountrycode)) {
                    $loc_country_code = $steam_user_data->loccountrycode;
                }
                
                $loc_state_code = NULL;
                
                if(!empty($steam_user_data->locstatecode)) {
                    $loc_state_code = $steam_user_data->locstatecode;
                }
                
                $loc_city_id = NULL;
                
                if(!empty($steam_user_data->loccityid)) {
                    $loc_city_id = $steam_user_data->loccityid;
                }
                
                $steam_user_record = array(
                    'communityvisibilitystate' => $community_visibility_state,
                    'profilestate' => $profile_state,
                    'personaname' => $persona_name,
                    'lastlogoff' => $last_logoff,
                    'profileurl' => $profile_url,
                    'avatar' => $avatar,
                    'avatarmedium' => $avatar_medium,
                    'avatarfull' => $avatar_full,
                    'personastate' => $persona_state,
                    'realname' => $real_name,
                    'primaryclanid' => $primary_clan_id,
                    'timecreated' => $time_created,
                    'personastateflags' => $persona_state_flags,
                    'loccountrycode' => $loc_country_code,
                    'locstatecode' => $loc_state_code,
                    'loccityid' => $loc_city_id,
                    'updated' => date('Y-m-d H:i:s')
                );
                                                    
                db()->update('steam_users', $steam_user_record, array (
                    'steam_user_id' => $steam_user_id
                ));
                
                $steam_user_record['steam_user_id'] = $steam_user_id;
                
                $cache->hMset("steam_users:{$steam_user_id}", $steam_user_record);
            }
        }
        else {
            if($verbose_output) {
                $framework->cout("No users were found for group {$group_number}.\n");
            }
        }
        
        //Pause the script for a second to prevent throttling issues with too many requests per second
        sleep(1);
    }
}
else {
    if($verbose_output) {
        $framework->cout("No steam users have been grouped. Exiting.\n");
    }
}

if($verbose_output) {
    $framework->cout("Done!.\n");
}