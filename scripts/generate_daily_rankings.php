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
use \Framework\Core\Modes\Cli\Framework;
use \Framework\Modules\Module;

require_once(dirname(dirname(dirname(__DIR__))) . '/framework/core/modes/cli/framework.class.php');

function display_help() {
    die(
        "\n================================================================================\n" . 
        "\nThis script generates daily rankings from leaderboard entry data.\n" . 
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
    WHERE c.name = 'cadence'
        AND l.is_daily = 1
        AND l.is_co_op = 0
        AND l.is_custom = 0
        AND l.is_all_character = 0
        AND l.is_deathless = 0
        AND l.is_story_mode = 0
");

/* ----- First pass to gather rank information ----- */

if($verbose_output) {
    $framework->coutLine("Executing first pass to gather rank information.");
}

$leaderboard_snapshot_ids = array();
$daily_dates = array();
$user_total_points = array();
$leaderboard_stats = array();

while($latest_leaderboard_entry = $latest_leaderboard_entries->fetch(PDO::FETCH_ASSOC)) {
    $leaderboard_snapshot_id = $latest_leaderboard_entry['leaderboard_snapshot_id'];
    $daily_date = $latest_leaderboard_entry['daily_date'];
    
    $leaderboard_snapshot_ids[$leaderboard_snapshot_id] = $leaderboard_snapshot_id;
    $daily_dates[$daily_date] = $daily_date;
    
    $steam_user_id = $latest_leaderboard_entry['steam_user_id'];
    
    if(empty($leaderboard_stats[$steam_user_id])) {
        $leaderboard_stats[$steam_user_id] = array(
            'steam_user_id' => $steam_user_id,
            'first_place_ranks' => 0,
            'top_5_ranks' => 0,
            'top_10_ranks' => 0,
            'top_20_ranks' => 0,
            'top_50_ranks' => 0,
            'top_100_ranks' => 0,
            'total_points' => 0,
            'total_dailies' => 0,
            'total_wins' => 0,
            'sum_of_ranks' => 0,
            'number_of_ranks' => 0
        );
        
        $user_total_points[$steam_user_id] = 0;
    }
    
    $leaderboard_stat_record = $leaderboard_stats[$steam_user_id];
    
    $rank = $latest_leaderboard_entry['rank'];
    
    if($rank == 1) {
        $leaderboard_stat_record['first_place_ranks'] += 1;
    }
    elseif($rank >= 1 && $rank <= 5) {
        $leaderboard_stat_record['top_5_ranks'] += 1;
    }
    elseif($rank >= 1 && $rank <= 10) {
        $leaderboard_stat_record['top_10_ranks'] += 1;
    }
    elseif($rank >= 1 && $rank <= 20) {
        $leaderboard_stat_record['top_20_ranks'] += 1;
    }
    elseif($rank >= 1 && $rank <= 50) {
        $leaderboard_stat_record['top_50_ranks'] += 1;
    }
    elseif($rank >= 1 && $rank <= 100) {
        $leaderboard_stat_record['top_100_ranks'] += 1;
    }
    
    $total_points = (int)(1.05 * (101 - $rank)); 
    
    if($total_points < 0) {
        $total_points = 0;
    }
    
    $leaderboard_stat_record['total_points'] += $total_points;
    $user_total_points[$steam_user_id] += $total_points;
    
    $leaderboard_stat_record['total_dailies'] += 1;
    
    if($latest_leaderboard_entry['details'] == '0300000005000000') {
        $leaderboard_stat_record['total_wins'] += 1;
    }
    
    $leaderboard_stat_record['sum_of_ranks'] += $rank;
    $leaderboard_stat_record['number_of_ranks'] += 1;
    
    $leaderboard_stats[$steam_user_id] = $leaderboard_stat_record;
}

if($verbose_output) {
    $framework->coutLine("Creating daily rank entry and marking it as the latest.");
}

//Mark all old daily rankings as not the latest
db()->update('daily_rankings', array(
    'latest' => 0
), array(
    'latest' => 1
));

$daily_ranking_id = db()->insert('daily_rankings', array(
    'latest' => 1,
    'created' => date('Y-m-d H:i:s')
));

if($verbose_output) {
    $framework->coutLine("Linking leaderboard snapshots to this daily ranking.");
}

//Add all leaderboard snapshot ids for this ranking to its own table
if(!empty($leaderboard_snapshot_ids)) {
    foreach($leaderboard_snapshot_ids as $leaderboard_snapshot_id) {
        db()->insert('daily_ranking_leaderboard_snapshots', array(
            'daily_ranking_id' => $daily_ranking_id,
            'leaderboard_snapshot_id' => $leaderboard_snapshot_id
        ));
    }
}

/* ----- Second pass to calculate rankings ----- */

if($verbose_output) {
    $framework->coutLine("Executing second pass to calculate rankings.");
}

foreach($leaderboard_stats as $leaderboard_user_index => &$leaderboard_user) {
    $leaderboard_user['daily_ranking_id'] = $daily_ranking_id;

    $leaderboard_user['average_place'] = (int)round($leaderboard_user['sum_of_ranks'] / $leaderboard_user['number_of_ranks']);
    
    $leaderboard_user['points_per_day'] = (int)round($leaderboard_user['total_points'] / $leaderboard_user['total_dailies']);
}

/* ----- Third pass to add actual user ranks ----- */

if($verbose_output) {
    $framework->coutLine("Executing third pass to add overall ranks.");
}

arsort($user_total_points);

$rank = 1;

foreach($user_total_points as $steam_user_id => &$total_points) {
    $leaderboard_stats[$steam_user_id]['rank'] = $rank;
    
    $rank += 1;
}

unset($user_total_points);

/* ----- Fourth pass to (finally) insert into database ----- */

if($verbose_output) {
    $framework->coutLine("Executing fourth pass to add finalized data into database.");
}

foreach($leaderboard_stats as &$leaderboard_user) {
    db()->insert('daily_ranking_entries', $leaderboard_user);
}