<?php
/**
* Retrieves leaderboard stats for Crypt of the Necrodancer.
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
        "\nThis script retrieves all leaderboards and entries for them.\n" . 
        "\nOptions:\n" . 
        "\n-h Displays this help." . 
        "\n================================================================================\n"
    );
}

function convertXmlToObject(SimpleXMLElement $xml_object, $namespace = '') {
    $object_structure = new stdClass();
    
    //Add any XML attributes as properties of the current object structure
    $xml_attributes = $xml_object->attributes();

    if(!empty($xml_attributes)) {
        foreach($xml_attributes as $attribute_name => $attribute_value) {
            $object_structure->$attribute_name = (string)$attribute_value;
        }
    }
    
    $children = NULL;
    
    if(!empty($namespace)) {
        $children = $xml_object->children($namespace, true);
    }
    else {
        $children = $xml_object->children();
    }

    if(!empty($children)) {
        //If there are any children of this xml element then add those as properties of the current object structure
        foreach($children as $child_name => $child) {
            //Convert this child element into an object structure via recursive call to this function
            $child_object = convertXmlToObject($child, $namespace);
            
            /*
                If the processed child only has 1 property and that property is 'value' 
                (a value set for scalar vaules by this function) then set the value of this
                child as that directly.
            */ 
            if(count($child_object) <= 1 && isset($child_object->value)) {
                $child_object = $child_object->value;
            }
            
            /*
                Convert the child object into an array. If the array version of the object has no
                elements, indicating an empty object, then set the child object to NULL.
            */
            $child_object_array = (array)$child_object;
            
            if(count($child_object_array) == 0) {
                $child_object = NULL;
            }

            if(!isset($object_structure->$child_name)) {
                $object_structure->$child_name = $child_object;
            }
            /*
                If the child name already exists then the property is an array. Convert the
                child property into an array and append additional child elements of this name
                as new array elements.
            */
            else {
                if(!is_array($object_structure->$child_name)) {
                    $current_child = $object_structure->$child_name;
                    
                    $object_structure->$child_name = array(
                        $current_child
                    );
                }
                
                $object_structure->{$child_name}[] = $child_object;
            }
        }
    }
    //If the current object structure has no children but has a value then set that as a property
    else {
        $value = (string)$xml_object;
    
        if(strlen($value) > 0) {
            $object_structure->value = $value;
        }
    }
    
    return $object_structure;
}

function get_leaderboard_users($leaderboard_id, $leaderboard_snapshot_id, $url, $verbose_output) {
    $framework = Framework::getInstance();

    $leaderboard_users_xml = file_get_contents($url);
    
    if(!empty($leaderboard_users_xml)) {
        $leaderboard_users = convertXmlToObject(new SimpleXMLElement($leaderboard_users_xml));
        
        $entries = array();

        if(!empty($leaderboard_users->entries->entry)) {
            if(is_array($leaderboard_users->entries->entry)) {
                $entries = $leaderboard_users->entries->entry;
            }
            else {
                $entries[] = $leaderboard_users->entries->entry;
            }
        }

        if(!empty($entries)) {
            $entry_count = count($entries);
        
            if($verbose_output) {
                $framework->cout("{$entry_count} snapshot entries found, processing entries.\n");
            }
        
            foreach($entries as $leaderboard_user) {
                if(!empty(Cache::$steam_users[$leaderboard_user->steamid])) {
                    if($verbose_output) {
                        $framework->cout("Hitting cache for user id {$leaderboard_user->steamid}.\n");
                    }
                
                    $steam_user_id = Cache::$steam_users[$leaderboard_user->steamid];
                }
                else {
                    if($verbose_output) {
                        $framework->cout("Adding user {$leaderboard_user->steamid} to database.\n");
                    }
                                                        
                    $steam_user_id = db()->insert('steam_users', array(
                        'steamid' => $leaderboard_user->steamid,
                    ), 'add_steam_user');
                    
                    Cache::$steam_users[$leaderboard_user->steamid] = $steam_user_id;
                }
                
                if($verbose_output) {
                    $framework->cout("Adding entry for this snapshot.\n");
                }
                
                
                $score = $leaderboard_user->score;
                
                $time = NULL;
                
                if(!empty($is_speedrun)) {
                    $time = 100000000 - $score;
                }
                
                $is_win = 0;
                
                if($leaderboard_user->details == '0300000005000000') {
                    $is_win = 1;
                }
            
                $leaderboard_entry_id = db()->insert('leaderboard_entries', array(
                    'leaderboard_id' => $leaderboard_id,
                    'leaderboard_snapshot_id' => $leaderboard_snapshot_id,
                    'steam_user_id' => $steam_user_id,
                    'score' => $score,
                    'rank' => $leaderboard_user->rank,
                    'ugcid' => $leaderboard_user->ugcid,
                    'details' => $leaderboard_user->details,
                    'time' => $time,
                    'is_win' => $is_win
                ), 'add_leaderboard_entry');
            }
        }
        
        if(!empty(!empty($leaderboard_users->nextRequestURL))) {
            $next_request_url = trim($leaderboard_users->nextRequestURL);
        
            if(!empty($next_request_url)) {
                if($verbose_output) {
                    $framework->cout("Loading next page of users.\n");
                }
            
                get_leaderboard_users($leaderboard_id, $leaderboard_snapshot_id, $next_request_url, $verbose_output);
            }
        }
    }
}

class Cache {
    public static $stored_leaderboards = array();
    
    public static $steam_users = array();
    
    public static $characters = array();
}

$framework = new Framework('vh', true);

if(isset($framework->arguments->h)) {
    display_help();
}

$verbose_output = false;

if(isset($framework->arguments->v)) {
    $verbose_output = true;
}

$module = new Module('necrolab');

$current_date = date('Y-m-d');

$leaderboards_xml = file_get_contents($module->configuration->leaderboard_url);

$leaderboards = convertXmlToObject(new SimpleXMLElement($leaderboards_xml));

if(!empty($leaderboards->leaderboard)) {
    if($verbose_output) {
        $framework->cout("Loading leaderboard cache.\n");
    }
    
    $stored_leaderboards_ungrouped = db()->getAll("
        SELECT *
        FROM leaderboards
    ");
    
    if(!empty($stored_leaderboards_ungrouped)) {
        foreach($stored_leaderboards_ungrouped as $stored_leaderboard_ungrouped) {
            Cache::$stored_leaderboards[$stored_leaderboard_ungrouped['lbid']] = $stored_leaderboard_ungrouped;
        }
        
        unset($stored_leaderboards_ungrouped);
    }

    if($verbose_output) {
        $framework->cout("Loading user cache.\n");
    }

    Cache::$steam_users = db()->getMappedColumn("
        SELECT
            steamid,
            steam_user_id
        FROM steam_users
    ");
    
    if($verbose_output) {
        $framework->cout("Loading character cache.\n");
    }
    
    Cache::$characters = db()->getMappedColumn("
        SELECT
            name,
            character_id
        FROM characters
    ");

    foreach($leaderboards->leaderboard as $leaderboard) {
        if($verbose_output) {
            $framework->cout("===== Working on leaderboard id {$leaderboard->lbid}. =====\n");
        }
            
        $leaderboard_id = NULL;
        $steam_user_id = NULL;
        $daily_date_timestamp = NULL;
        $current_date_timestamp = strtotime(date('Y-m-d'));
        $is_speedrun = 0;
        $is_prod = 0;
        
        if(!empty(Cache::$stored_leaderboards[$leaderboard->lbid])) {
            if($verbose_output) {
                $framework->cout("Hitting cache for leaderboard id.\n");
            }
            
            $stored_leaderboard = Cache::$stored_leaderboards[$leaderboard->lbid];
            
            $leaderboard_id = $stored_leaderboard['leaderboard_id'];
            
            $daily_date = $stored_leaderboard['daily_date'];
            
            if(!empty($daily_date)) {
                $daily_date_timestamp = strtotime($daily_date);
            }
        }
        else {
            if($verbose_output) {
                $framework->cout("Adding new record for leaderboard id.\n");
            }
            
            $leaderboard_name = strtolower($leaderboard->name);
            
            $character_id = NULL;
            
            /*
                Retrieve which character this leaderboard is for.
                This is done by looking for a case insensitive version of each charcter's name.
                This is sloppy, but the only way I could find to fairly reliably get the character of each leaderboard with the data given.
            */
            if(strpos($leaderboard_name, 'bard') !== false) {
                $character_id = Cache::$characters['bard'];
            }
            elseif(strpos($leaderboard_name, 'aria') !== false) {
                $character_id = Cache::$characters['aria'];
            }
            elseif(strpos($leaderboard_name, 'monk') !== false) {
                $character_id = Cache::$characters['monk'];
            }
            elseif(strpos($leaderboard_name, 'bolt') !== false) {
                $character_id = Cache::$characters['bolt'];
            }
            elseif(strpos($leaderboard_name, 'dove') !== false) {
                $character_id = Cache::$characters['dove'];
            }
            elseif(strpos($leaderboard_name, 'eli') !== false) {
                $character_id = Cache::$characters['eli'];
            }
            elseif(strpos($leaderboard_name, 'melody') !== false) {
                $character_id = Cache::$characters['melody'];
            }
            elseif(strpos($leaderboard_name, 'dorian') !== false) {
                $character_id = Cache::$characters['dorian'];
            }
            elseif(strpos($leaderboard_name, 'coda') !== false) {
                $character_id = Cache::$characters['coda'];
            }
            elseif(strpos($leaderboard_name, 'ghost') !== false) {
                $character_id = Cache::$characters['ghost'];
            }
            elseif(strpos($leaderboard_name, 'pacifist') !== false) {
                $character_id = Cache::$characters['pacifist'];
            }
            elseif(strpos($leaderboard_name, 'thief') !== false) {
                $character_id = Cache::$characters['thief'];
            }
            //If nobody else assume it's Cadence
            else {
                $character_id = Cache::$characters['cadence'];
            }
            
            $is_speedrun = 0;
            $is_custom = 0;
            $is_co_op = 0;
            $is_seeded = 0;
            $is_daily = 0;
            $daily_date = NULL;
            $is_score_run = 0;
            $is_all_character = 0;
            $is_deathless = 0;
            $is_story_mode = 0;
            $is_dev = 0;
            $is_prod = 0;
            
            if(strpos($leaderboard_name, 'speedrun') !== false) {
                $is_speedrun = 1;
                $is_score_run = 0;
            }
            
            if(strpos($leaderboard_name, 'custom') !== false) {
                $is_custom = 1;
            }
            
            if(strpos($leaderboard_name, 'co-op') !== false) {
                $is_co_op = 1;
            }
            
            if(strpos($leaderboard_name, 'seeded') !== false) {
                $is_seeded = 1;
            }
            
            if(strpos($leaderboard_name, 'hardcore') !== false || strpos($leaderboard_name, 'all zones') !== false) {
                $is_speedrun = 0;
                $is_score_run = 1;
            }
            
            if(strpos($leaderboard_name, 'all chars') !== false) {
                $is_all_character = 1;
                $character_id = Cache::$characters['all'];
            }
            
            if(strpos($leaderboard_name, 'deathless') !== false) {
                $is_deathless = 1;
            }
            
            if(strpos($leaderboard_name, 'story') !== false) {
                $is_story_mode = 1;
            }
            
            if(strpos($leaderboard_name, 'dev') !== false) {
                $is_dev = 1;
            }
            
            if(strpos($leaderboard_name, 'prod') !== false) {
                $is_prod = 1;
            }
            
            /*
                If this run is a daily then grab the date it is for.
                Date matching solution found at: http://stackoverflow.com/a/7645146
                Date filtering solution found at: http://stackoverflow.com/a/4639488  
            */
            $unformatted_daily_date = preg_replace("/[^0-9\/]/", "", $leaderboard_name);
            
            if(!empty($unformatted_daily_date)) {
                $is_daily = 1;
                $is_speedrun = 0;
                $is_score_run = 1;

                $daily_date_object = DateTime::createFromFormat('d/m/Y', $unformatted_daily_date);
                
                $daily_date = $daily_date_object->format('Y-m-d');
                
                $daily_date_timestamp = strtotime($daily_date);
            }
            
            $leaderboard_record = array(
                'name' => $leaderboard->name,
                'url' => $leaderboard->url,
                'lbid' => $leaderboard->lbid,
                'display_name' => $leaderboard->display_name,
                'entries' => $leaderboard->entries,
                'sortmethod' => $leaderboard->sortmethod,
                'displaytype' => $leaderboard->displaytype,
                'onlytrustedwrites' => $leaderboard->onlytrustedwrites,
                'onlyfriendsreads' => $leaderboard->onlyfriendsreads,
                'character_id' => $character_id,
                'is_speedrun' => $is_speedrun,
                'is_custom' => $is_custom,
                'is_co_op' => $is_co_op,
                'is_seeded' => $is_seeded,
                'is_daily' => $is_daily,
                'daily_date' => $daily_date,
                'is_score_run' => $is_score_run,
                'is_all_character' => $is_all_character,
                'is_deathless' => $is_deathless,
                'is_story_mode' => $is_story_mode,
                'is_dev' => $is_dev,
                'is_prod' => $is_prod
            );
        
            $leaderboard_id = db()->insert('leaderboards', $leaderboard_record, 'add_leaderboard');
            
            $leaderboard_record['leaderboard_id'] = $leaderboard_id;
            
            Cache::$stored_leaderboards[$leaderboard->lbid] = $leaderboard_record;
        }

        //If this leaderboard is not a daily or it is a daily and either today's or tomorrow's (an active daily) then proceed to create a snapshot
        if(!empty($is_prod) && (empty($daily_date_timestamp) || $daily_date_timestamp >= $current_date_timestamp)) {
            if($verbose_output) {
                $framework->cout("Checking to see if a leaderboard snapshot exists for today.\n");
            }
            
            $leaderboard_snapshot_id = db()->getOne("
                SELECT leaderboard_snapshot_id
                FROM leaderboard_snapshots
                WHERE leaderboard_id = ?
                    AND date = ?
            ", array(
                $leaderboard_id,
                $current_date
            ));
    
            if(empty($leaderboard_snapshot_id)) {
                if($verbose_output) {
                    $framework->cout("No existing leaderboard snapshot was found for today. Creating a new one.\n");
                }
            
                $leaderboard_snapshot_id = db()->insert('leaderboard_snapshots', array(
                    'leaderboard_id' => $leaderboard_id,
                    'date' => $current_date,
                    'created' => date('Y-m-d H:i:s')
                ), 'add_leaderboard_snapshot');
            }
            else {
                if($verbose_output) {
                    $framework->cout("An existing snapshot was found for today. Deleting existing data to replace with new records.\n");
                }
            
                db()->update('leaderboard_snapshots', array(
                    'updated' => date('Y-m-d H:i:s')
                ), array(
                    'leaderboard_snapshot_id' => $leaderboard_snapshot_id
                ), array(), 'update_leaderboard_snapshot');
            
                db()->delete('leaderboard_entries', array(
                    'leaderboard_snapshot_id' => $leaderboard_snapshot_id
                ), array(), 'delete_leaderboard_entries');
            }
            
            if($verbose_output) {
                $framework->cout("Updating leaderboard with latest snapshot.\n");
            }
            
            db()->update('leaderboards', array(
                'last_snapshot_id' => $leaderboard_snapshot_id
            ), array(
                'leaderboard_id' => $leaderboard_id
            ), array(), 'update_leaderboard_latest_snapshot');

            get_leaderboard_users($leaderboard_id, $leaderboard_snapshot_id, $leaderboard->url, $verbose_output);
        }
        else {
            if($verbose_output) {
                $framework->cout("Leaderboard does not need a snapshot. Skipping.\n");
            }
        }
    }
}

if($verbose_output) {
    $framework->cout("Done!.\n");
}