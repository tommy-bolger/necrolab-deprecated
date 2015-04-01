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
        "\nThis script generates power rankings from leaderboard entry data.\n" . 
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
    WHERE c.is_active = 1
        AND (l.is_score_run = 1 OR l.is_speedrun = 1)
        AND l.is_custom = 0
        AND l.is_co_op = 0
        AND l.is_seeded = 0
        AND l.is_daily = 0
        AND l.is_story_mode = 0;
");

/* ----- First pass to gather rank information ----- */

if($verbose_output) {
    $framework->coutLine("Executing first pass to gather rank information.");
}

$leaderboard_snapshot_ids = array();
$leaderboard_stats = array();

while($latest_leaderboard_entry = $latest_leaderboard_entries->fetch(PDO::FETCH_ASSOC)) {
    $leaderboard_snapshot_id = $latest_leaderboard_entry['leaderboard_snapshot_id'];
    
    $leaderboard_snapshot_ids[$leaderboard_snapshot_id] = $leaderboard_snapshot_id;

    $steam_user_id = $latest_leaderboard_entry['steam_user_id'];
    $character_column_prefix = $latest_leaderboard_entry['character_name'];
    
    $rank_column_name = $character_column_prefix;
    $leaderboard_entry_id_name = $character_column_prefix;
    $time = NULL;
    $score = NULL;

    if(!empty($latest_leaderboard_entry['is_speedrun'])) {
        $rank_column_name .= "_speed_rank";
        $leaderboard_entry_id_name .= '_speed_leaderboard_entry_id';
        $time = $latest_leaderboard_entry['time'];
    }
    else {
        $rank_column_name .= "_score_rank";
        $leaderboard_entry_id_name .= '_score_leaderboard_entry_id';
        $score = $latest_leaderboard_entry['score'];
    }
    
    $leaderboard_stats[$steam_user_id]['steam_user_id'] = $steam_user_id; 
    $leaderboard_stats[$steam_user_id][$rank_column_name] = $latest_leaderboard_entry['rank'];
    $leaderboard_stats[$steam_user_id][$leaderboard_entry_id_name] = $latest_leaderboard_entry['leaderboard_entry_id'];
    $leaderboard_stats[$steam_user_id]["{$character_column_prefix}_speed_time"] = $time;
    $leaderboard_stats[$steam_user_id]["{$character_column_prefix}_score"] = $score;
}

if($verbose_output) {
    $framework->coutLine("Creating power rank entry and marking it as the latest.");
}

$power_ranking_id = db()->insert('power_rankings', array(
    'created' => date('Y-m-d H:i:s')
));

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

/* ----- Second pass to calculate rankings ----- */

if($verbose_output) {
    $framework->coutLine("Executing second pass to calculate rankings.");
}

$user_top_10_bonus_scores = array();

foreach($leaderboard_stats as $leaderboard_user_index => &$leaderboard_user) {
    $leaderboard_user['power_ranking_id'] = $power_ranking_id;

    $speed_total = NULL;
    $score_total = NULL;
    $base = NULL;
    $weighted = NULL;
    $top_10_bonus = NULL;
    
    $number_of_speed_ranks = 0;
    $speed_rank_sum = 0;
    
    $number_of_score_ranks = 0;
    $score_rank_sum = 0;
    
    $total_number_of_ranks = 0;
    $total_rank_sum = 0;
    
    $weighted_speed_rank_sum = 0;
    $unweighted_speed_rank_sum = 0;
    $number_of_weighted_speedrun_ranks = 0;
    $number_of_unweighted_speedrun_ranks = 0;
    
    $weighted_score_rank_sum = 0;
    $unweighted_score_rank_sum = 0;
    $number_of_weighted_score_ranks = 0;
    $number_of_unweighted_score_ranks = 0;
    
    $total_1st_ranks = 0;
    $total_2nd_ranks = 0;
    $total_3rd_ranks = 0;
    $total_4th_ranks = 0;
    $total_5th_ranks = 0;
    $total_6th_ranks = 0;
    $total_7th_ranks = 0;
    $total_8th_ranks = 0;
    $total_9th_ranks = 0;
    $total_10th_ranks = 0;

    foreach($leaderboard_user as $leaderboard_stat_name => &$leaderboard_stat) {    
        if(strpos($leaderboard_stat_name, '_rank') !== false && !empty($leaderboard_stat)) {
            if(strpos($leaderboard_stat_name, '_speed') !== false) {
                $number_of_speed_ranks += 1;
                $speed_rank_sum += $leaderboard_stat;
                
                if(strpos($leaderboard_stat_name, 'cadence') !== false) {
                    $number_of_weighted_speedrun_ranks += 1;
                    $weighted_speed_rank_sum += $leaderboard_stat;
                }
                elseif(strpos($leaderboard_stat_name, 'bard') !== false) {
                    $number_of_weighted_speedrun_ranks += 1;
                    $weighted_speed_rank_sum += $leaderboard_stat;
                }
                else {
                    $number_of_unweighted_speedrun_ranks += 1;
                    $unweighted_speed_rank_sum += $leaderboard_stat;
                }
            }
            
            if(strpos($leaderboard_stat_name, '_score') !== false) {
                $number_of_score_ranks += 1;
                $score_rank_sum += $leaderboard_stat;
                
                if(strpos($leaderboard_stat_name, 'cadence') !== false) {
                    $number_of_weighted_score_ranks += 1;
                    $weighted_score_rank_sum += $leaderboard_stat;
                }
                elseif(strpos($leaderboard_stat_name, 'bard') !== false) {
                    $number_of_weighted_score_ranks += 1;
                    $weighted_score_rank_sum += $leaderboard_stat;
                }
                else {
                    $number_of_unweighted_score_ranks += 1;
                    $unweighted_score_rank_sum += $leaderboard_stat;
                }
            }
            
            $total_number_of_ranks += 1;
            $total_rank_sum += $leaderboard_stat;
            
            switch($leaderboard_stat) {
                case 1:
                    $total_1st_ranks += 1;
                    break;
                case 2:
                    $total_2nd_ranks += 1;
                    break;
                case 3:
                    $total_3rd_ranks += 1;
                    break; 
                case 4:
                    $total_4th_ranks += 1;
                    break;
                case 5:
                    $total_5th_ranks += 1;
                    break;
                case 6:
                    $total_6th_ranks += 1;
                    break;
                case 7:
                    $total_7th_ranks += 1;
                    break;
                case 8:
                    $total_8th_ranks += 1;
                    break;
                case 9:
                    $total_9th_ranks += 1;
                    break;
                case 10:
                    $total_10th_ranks += 1;
                    break;
            }
        }
    }
    
    $speed_total = (101 * $number_of_speed_ranks) - $speed_rank_sum;
    
    
    if($speed_total < 0) {
        $speed_total = 0;
    }
    
    $score_total = (101 * $number_of_score_ranks) - $score_rank_sum;
    
    if($score_total < 0) {
        $score_total = 0;
    }
    
    $base = (101 * $total_number_of_ranks) - $total_rank_sum;
    
    if($base < 0) {
        $base = 0;
    }
    
    $weighted_rank_score = (303 * $number_of_weighted_speedrun_ranks + $number_of_weighted_score_ranks) - (3 * ($weighted_speed_rank_sum + $weighted_score_rank_sum));
    
    if($weighted_rank_score < 0) {
        $weighted_rank_score = 0;
    }
    
    $unweighted_rank_score = ((101 * ($number_of_unweighted_speedrun_ranks + $number_of_unweighted_score_ranks)) - ($number_of_unweighted_speedrun_ranks + $number_of_unweighted_score_ranks));
    
    if($unweighted_rank_score < 0) {
        $unweighted_rank_score = 0;
    }
    
    $weighted = $weighted_rank_score + $unweighted_rank_score;
        
    $top_10_bonus = 
        $base + 
        (100 * $total_1st_ranks) + 
        (90 * $total_2nd_ranks) + 
        (80 * $total_3rd_ranks) + 
        (70 * $total_4th_ranks) + 
        (60 * $total_5th_ranks) + 
        (50 * $total_6th_ranks) + 
        (40 * $total_7th_ranks) + 
        (30 * $total_8th_ranks) + 
        (20 * $total_9th_ranks) + 
        (10 * $total_10th_ranks);
        
    $user_top_10_bonus_scores[$leaderboard_user['steam_user_id']] = $top_10_bonus;
    
    $leaderboard_user['speed_total'] = $speed_total;
    $leaderboard_user['score_total'] = $score_total; 
    $leaderboard_user['base'] = $base; 
    $leaderboard_user['weighted'] = $weighted; 
    $leaderboard_user['top_10_bonus'] = $top_10_bonus;  
}

/* ----- Third pass to add actual user ranks ----- */

if($verbose_output) {
    $framework->coutLine("Executing third pass to add overall ranks.");
}

arsort($user_top_10_bonus_scores);

$rank = 1;

foreach($user_top_10_bonus_scores as $steam_user_id => &$top_10_bonus_score) {
    $leaderboard_stats[$steam_user_id]['rank'] = $rank;
    
    $rank += 1;
}

unset($user_top_10_bonus_scores);

/* ----- Fourth pass to (finally) insert into database ----- */

if($verbose_output) {
    $framework->coutLine("Executing fourth pass to add finalized data into database.");
}

foreach($leaderboard_stats as &$leaderboard_user) {
    db()->insert('power_ranking_entries', $leaderboard_user);
}