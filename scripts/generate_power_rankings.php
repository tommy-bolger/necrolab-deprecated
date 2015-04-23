<?php
/**
* Generates the power rankings for Crypt of the Necrodancer.
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

// ------------------ NOW ALGORITHM: 1.7/(log(x/100+1.03)/log(10))

use \Framework\Core\Modes\Cli\Framework;
use \Framework\Modules\Module;

require_once(dirname(dirname(dirname(__DIR__))) . '/framework/core/modes/cli/framework.class.php');

function display_help() {
    die(
        "\n================================================================================\n" . 
        "\nThis script generates power rankings from leaderboard entry data.\n" . 
        "\nOptions:\n" . 
        "\n-h Displays this help." . 
        "\n================================================================================\n"
    );
}

function assign_ranks_to_points($rank_column_name, &$leaderboard_stats, &$user_point_totals) {
    $rank = 1;

    foreach($user_point_totals as $steam_user_id => &$user_point_total) {
        $leaderboard_stats[$steam_user_id][$rank_column_name] = $rank;
        
        $rank += 1;
    }
}

$framework = new Framework('vh', false);

if(isset($framework->arguments->h)) {
    display_help();
}

$verbose_output = false;

if(isset($framework->arguments->v)) {
    $verbose_output = true;
}

$current_date = date('Y-m-d');

$latest_leaderboard_entries = db()->prepareExecuteQuery("
    SELECT 
        *,
        ls.leaderboard_snapshot_id,     
        le.steam_user_id,        
        c.name AS character_name,
        le.rank,
        le.leaderboard_entry_id,
        le.score,
        le.time      
    FROM leaderboards l
    JOIN leaderboard_snapshots ls ON ls.leaderboard_snapshot_id = l.last_snapshot_id
    JOIN leaderboard_entries le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
    JOIN characters c ON c.character_id = l.character_id
    WHERE c.is_active = 1
        AND (
            l.is_score_run = 1 
            OR l.is_speedrun = 1
        )
        AND l.is_custom = 0
        AND l.is_co_op = 0
        AND l.is_seeded = 0
        AND l.is_daily = 0
        AND l.is_dev = 0
        AND l.is_prod = 1           
");

/* ----- First pass to gather rank information ----- */

if($verbose_output) {
    $framework->coutLine("Executing first pass to gather rank information and calculate points.");
}

$leaderboard_snapshot_ids = array();
$leaderboard_stats = array();

while($latest_leaderboard_entry = $latest_leaderboard_entries->fetch(PDO::FETCH_ASSOC)) {
    $leaderboard_snapshot_id = $latest_leaderboard_entry['leaderboard_snapshot_id'];
    
    $leaderboard_snapshot_ids[$leaderboard_snapshot_id] = $leaderboard_snapshot_id;

    $steam_user_id = $latest_leaderboard_entry['steam_user_id'];
    $character_column_prefix = $latest_leaderboard_entry['character_name'];
    $total_points_column_name = '';
    
    $is_deathless = $latest_leaderboard_entry['is_deathless'];        
    
    if(!empty($is_deathless)) {
        $character_column_prefix .= "_deathless";
        $total_points_column_name = 'deathless_';
    }
    
    $rank_column_name = $character_column_prefix;
    $rank_points_column_name = $character_column_prefix;
    $time = NULL;
    $score = NULL;

    if(!empty($latest_leaderboard_entry['is_speedrun'])) {
        $rank_column_name .= "_speed_rank";
        $rank_points_column_name .= "_speed_rank_points";
        $total_points_column_name .= 'speed_rank_points_total';
        $time = $latest_leaderboard_entry['time'];
    }
    else {
        $rank_column_name .= "_score_rank";
        $rank_points_column_name .= "_score_rank_points";
        $total_points_column_name .= 'score_rank_points_total';
        $score = $latest_leaderboard_entry['score'];
    }
    
    $rank = $latest_leaderboard_entry['rank'];
    
    $rank_points = 1.7 / (log($rank / 100 + 1.03) / log(10));
    
    if(!isset($leaderboard_stats[$steam_user_id][$total_points_column_name])) {
        $leaderboard_stats[$steam_user_id][$total_points_column_name] = 0;
    }
    
    if(empty($leaderboard_stats[$steam_user_id]['total_points'])) {
        $leaderboard_stats[$steam_user_id]['total_points'] = 0;
    }
    
    $leaderboard_stats[$steam_user_id]['steam_user_id'] = $steam_user_id; 
    $leaderboard_stats[$steam_user_id][$rank_column_name] = $rank;
    $leaderboard_stats[$steam_user_id][$rank_points_column_name] = $rank_points;
    
    /*
        Deathless score is only supported by the game at the moment. Speedrun, all character, and story mode are not.
        These ranks will be collected but not added into the overall ranks and point total.
    */
    if(empty($is_deathless) || (!empty($is_deathless) && empty($latest_leaderboard_entry['is_story']) && empty($latest_leaderboard_entry['is_all_character']) && empty($latest_leaderboard_entry['is_speedrun']))) {
        $leaderboard_stats[$steam_user_id][$total_points_column_name] += $rank_points;
        $leaderboard_stats[$steam_user_id]['total_points'] += $rank_points;
    }
    
    $leaderboard_stats[$steam_user_id]["{$character_column_prefix}_speed_time"] = $time;
    $leaderboard_stats[$steam_user_id]["{$character_column_prefix}_score"] = $score;
}

if($verbose_output) {
    $framework->coutLine("Checking to see if there is an existing power ranking for today.");
}

$power_ranking_id = db()->getOne("
    SELECT power_ranking_id
    FROM power_rankings
    WHERE date = ?
", array(
    $current_date
));

if(empty($power_ranking_id)) {
    if($verbose_output) {
        $framework->coutLine("No power ranking for today was found. Creating a new one.");
    }
    
    $power_ranking_id = db()->insert('power_rankings', array(
        'date' => $current_date,
        'created' => date('Y-m-d H:i:s')
    ));
}
else {
    if($verbose_output) {
        $framework->coutLine("An existing power ranking for today was found. Deleting existing entries to replace with new ones.");
    }
    
    db()->update('power_rankings', array(
        'updated' => date('Y-m-d H:i:s')
    ), array(
        'power_ranking_id' => $power_ranking_id
    ));

    db()->delete('power_ranking_leaderboard_snapshots', array(
        'power_ranking_id' => $power_ranking_id
    ));
    
    db()->delete('power_ranking_entries', array(
        'power_ranking_id' => $power_ranking_id
    ));
}

//Mark this new power ranking as the latest one
db()->update('power_rankings', array(
    'latest' => 0
), array(
    'latest' => 1
));

db()->update('power_rankings', array(
    'latest' => 1
), array(
    'power_ranking_id' => $power_ranking_id
));

if($verbose_output) {
    $framework->coutLine("Linking leaderboard snapshots to this power ranking.");
}

//Add all leaderboard snapshot ids for this ranking to its own table
if(!empty($leaderboard_snapshot_ids)) {
    foreach($leaderboard_snapshot_ids as $leaderboard_snapshot_id) {
        db()->insert('power_ranking_leaderboard_snapshots', array(
            'power_ranking_id' => $power_ranking_id,
            'leaderboard_snapshot_id' => $leaderboard_snapshot_id
        ));
    }
}

/* ----- Second pass to group rankings ----- */

if($verbose_output) {
    $framework->coutLine("Executing second pass to group rankings and sort them.");
}

$user_speed_point_totals = array();
$user_deathless_speed_point_totals = array();
$user_score_point_totals = array();
$user_deathless_score_point_totals = array();
$user_total_point_totals = array();

foreach($leaderboard_stats as $leaderboard_user_index => &$leaderboard_user) {
    $steam_user_id = $leaderboard_user['steam_user_id'];

    if(!empty($leaderboard_user['speed_rank_points_total'])) {
        $user_speed_point_totals[$steam_user_id] = $leaderboard_user['speed_rank_points_total'];
    }
    
    if(!empty($leaderboard_user['deathless_speed_rank_points_total'])) {
        $user_deathless_speed_point_totals[$steam_user_id] = $leaderboard_user['deathless_speed_rank_points_total'];
    }
    
    if(!empty($leaderboard_user['score_rank_points_total'])) {
        $user_score_point_totals[$steam_user_id] = $leaderboard_user['score_rank_points_total'];
    }
    
    if(!empty($leaderboard_user['deathless_score_rank_points_total'])) {
        $user_deathless_score_point_totals[$steam_user_id] = $leaderboard_user['deathless_score_rank_points_total'];
    }
    
    $user_total_point_totals[$steam_user_id] = $leaderboard_user['total_points'];
}

/* ----- Third pass to add actual user ranks ----- */

if($verbose_output) {
    $framework->coutLine("Executing third pass to assign ranks.");
}

arsort($user_speed_point_totals);
arsort($user_deathless_speed_point_totals);
arsort($user_score_point_totals);
arsort($user_deathless_score_point_totals);
arsort($user_total_point_totals);

assign_ranks_to_points('speed_rank', $leaderboard_stats, $user_speed_point_totals);
unset($user_speed_point_totals);

assign_ranks_to_points('deathless_speed_rank', $leaderboard_stats, $user_deathless_speed_point_totals);
unset($user_deathless_speed_point_totals);

assign_ranks_to_points('score_rank', $leaderboard_stats, $user_score_point_totals);
unset($user_score_point_totals);

assign_ranks_to_points('deathless_score_rank', $leaderboard_stats, $user_deathless_score_point_totals);
unset($user_deathless_score_point_totals);

assign_ranks_to_points('rank', $leaderboard_stats, $user_total_point_totals);
unset($user_total_point_totals);


/* ----- Fourth pass to (finally) insert into database ----- */

if($verbose_output) {
    $framework->coutLine("Executing fourth pass to add finalized data into database.");
}

//This empty record will allow the same prepared statement to be reused for a potential performance gain.
$empty_entry_record = array(
    'power_ranking_id' => NULL,
    'steam_user_id' => NULL,
    'cadence_score_rank' => NULL,
    'cadence_score_rank_points' => NULL,
    'cadence_score' => NULL,
    'bard_score_rank' => NULL,
    'bard_score_rank_points' => NULL,
    'bard_score' => NULL,
    'monk_score_rank' => NULL,
    'monk_score_rank_points' => NULL,
    'monk_score' => NULL,
    'aria_score_rank' => NULL,
    'aria_score_rank_points' => NULL,
    'aria_score' => NULL,
    'bolt_score_rank' => NULL,
    'bolt_score_rank_points' => NULL,
    'bolt_score' => NULL,
    'dove_score_rank' => NULL,
    'dove_score_rank_points' => NULL,
    'dove_score' => NULL,
    'eli_score_rank' => NULL,
    'eli_score_rank_points' => NULL,
    'eli_score' => NULL,
    'melody_score_rank' => NULL,
    'melody_score_rank_points' => NULL,
    'melody_score' => NULL,
    'dorian_score_rank' => NULL,
    'dorian_score_rank_points' => NULL,
    'dorian_score' => NULL,
    'coda_score_rank' => NULL,
    'coda_score_rank_points' => NULL,
    'coda_score' => NULL,
    'all_score_rank' => NULL,
    'all_score_rank_points' => NULL,
    'all_score' => NULL,
    'story_score_rank' => NULL,
    'story_score_rank_points' => NULL,
    'story_score' => NULL,
    'score_rank_points_total' => NULL,
    'cadence_deathless_score_rank' => NULL,
    'cadence_deathless_score_rank_points' => NULL,
    'cadence_deathless_score' => NULL,
    'bard_deathless_score_rank' => NULL,
    'bard_deathless_score_rank_points' => NULL,
    'bard_deathless_score' => NULL,
    'monk_deathless_score_rank' => NULL,
    'monk_deathless_score_rank_points' => NULL,
    'monk_deathless_score' => NULL,
    'aria_deathless_score_rank' => NULL,
    'aria_deathless_score_rank_points' => NULL,
    'aria_deathless_score' => NULL,
    'bolt_deathless_score_rank' => NULL,
    'bolt_deathless_score_rank_points' => NULL,
    'bolt_deathless_score' => NULL,
    'dove_deathless_score_rank' => NULL,
    'dove_deathless_score_rank_points' => NULL,
    'dove_deathless_score' => NULL,
    'eli_deathless_score_rank' => NULL,
    'eli_deathless_score_rank_points' => NULL,
    'eli_deathless_score' => NULL,
    'melody_deathless_score_rank' => NULL,
    'melody_deathless_score_rank_points' => NULL,
    'melody_deathless_score' => NULL,
    'dorian_deathless_score_rank' => NULL,
    'dorian_deathless_score_rank_points' => NULL,
    'dorian_deathless_score' => NULL,
    'coda_deathless_score_rank' => NULL,
    'coda_deathless_score_rank_points' => NULL,
    'coda_deathless_score' => NULL,
    'all_deathless_score_rank' => NULL,
    'all_deathless_score_rank_points' => NULL,
    'all_deathless_score' => NULL,
    'story_deathless_score_rank' => NULL,
    'story_deathless_score_rank_points' => NULL,
    'story_deathless_score' => NULL,
    'deathless_score_rank_points_total' => NULL,
    'cadence_speed_rank' => NULL,
    'cadence_speed_rank_points' => NULL,
    'cadence_speed_time' => NULL,
    'bard_speed_rank' => NULL,
    'bard_speed_rank_points' => NULL,
    'bard_speed_time' => NULL,
    'monk_speed_rank' => NULL,
    'monk_speed_rank_points' => NULL,
    'monk_speed_time' => NULL,
    'aria_speed_rank' => NULL,
    'aria_speed_rank_points' => NULL,
    'aria_speed_time' => NULL,
    'bolt_speed_rank' => NULL,
    'bolt_speed_rank_points' => NULL,
    'bolt_speed_time' => NULL,
    'dove_speed_rank' => NULL,
    'dove_speed_rank_points' => NULL,
    'dove_speed_time' => NULL,
    'eli_speed_rank' => NULL,
    'eli_speed_rank_points' => NULL,
    'eli_speed_time' => NULL,
    'melody_speed_rank' => NULL,
    'melody_speed_rank_points' => NULL,
    'melody_speed_time' => NULL,
    'dorian_speed_rank' => NULL,
    'dorian_speed_rank_points' => NULL,
    'dorian_speed_time' => NULL,
    'coda_speed_rank' => NULL,
    'coda_speed_rank_points' => NULL,
    'coda_speed_time' => NULL,
    'all_speed_rank' => NULL,
    'all_speed_rank_points' => NULL,
    'all_speed_time' => NULL,
    'story_speed_rank' => NULL,
    'story_speed_rank_points' => NULL,
    'story_speed_time' => NULL,   
    'speed_rank_points_total' => NULL,
    'cadence_deathless_speed_rank' => NULL,
    'cadence_deathless_speed_rank_points' => NULL,
    'cadence_deathless_speed_time' => NULL,
    'bard_deathless_speed_rank' => NULL,
    'bard_deathless_speed_rank_points' => NULL,
    'bard_deathless_speed_time' => NULL,
    'monk_deathless_speed_rank' => NULL,
    'monk_deathless_speed_rank_points' => NULL,
    'monk_deathless_speed_time' => NULL,
    'aria_deathless_speed_rank' => NULL,
    'aria_deathless_speed_rank_points' => NULL,
    'aria_deathless_speed_time' => NULL,
    'bolt_deathless_speed_rank' => NULL,
    'bolt_deathless_speed_rank_points' => NULL,
    'bolt_deathless_speed_time' => NULL,
    'dove_deathless_speed_rank' => NULL,
    'dove_deathless_speed_rank_points' => NULL,
    'dove_deathless_speed_time' => NULL,
    'eli_deathless_speed_rank' => NULL,
    'eli_deathless_speed_rank_points' => NULL,
    'eli_deathless_speed_time' => NULL,
    'melody_deathless_speed_rank' => NULL,
    'melody_deathless_speed_rank_points' => NULL,
    'melody_deathless_speed_time' => NULL,
    'dorian_deathless_speed_rank' => NULL,
    'dorian_deathless_speed_rank_points' => NULL,
    'dorian_deathless_speed_time' => NULL,
    'coda_deathless_speed_rank' => NULL,
    'coda_deathless_speed_rank_points' => NULL,
    'coda_deathless_speed_time' => NULL,
    'all_deathless_speed_rank' => NULL,
    'all_deathless_speed_rank_points' => NULL,
    'all_deathless_speed_time' => NULL,
    'story_deathless_speed_rank' => NULL,
    'story_deathless_speed_rank_points' => NULL,
    'story_deathless_speed_time' => NULL,
    'deathless_speed_rank_points_total' => NULL,
    'total_points' => NULL,
    'speed_rank' => NULL,
    'deathless_speed_rank' => NULL,
    'score_rank' => NULL,
    'deathless_score_rank' => NULL,
    'rank' => NULL,
);

foreach($leaderboard_stats as &$leaderboard_user) {
    $leaderboard_user['power_ranking_id'] = $power_ranking_id;
    
    $entry_record = array_merge($empty_entry_record, $leaderboard_user);
    
    $power_ranking_entry_id = db()->insert('power_ranking_entries', $entry_record, 'power_ranking_entry');
    
    db()->update('steam_users', array(
        'latest_power_ranking_entry_id' => $power_ranking_entry_id,
    ), array(
        'steam_user_id' => $leaderboard_user['steam_user_id']
    ), array(), 'update_steam_user');
}