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
        "\nThis script caches the power rankings for retrieval without hitting the database.\n" . 
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

$cache = cache();

$current_rankings_exists = $cache->exists('latest_power_rankings');

$latest_power_rankings = db()->prepareExecuteQuery("
    SELECT
        pre.rank,
        su.personaname,
        pre.cadence_speed_rank,
        pre.bard_speed_rank,
        pre.monk_speed_rank,
        pre.aria_speed_rank,
        pre.bolt_speed_rank,
        pre.dove_speed_rank,
        pre.eli_speed_rank,
        pre.melody_speed_rank,
        pre.dorian_speed_rank,
        pre.cadence_score_rank,
        pre.bard_score_rank,
        pre.monk_score_rank,
        pre.aria_score_rank,
        pre.bolt_score_rank,
        pre.dove_score_rank,
        pre.eli_score_rank,
        pre.melody_score_rank,
        pre.dorian_score_rank,
        pre.speed_total,
        pre.score_total,
        pre.base,
        pre.weighted,
        pre.top_10_bonus,
        pre.steam_user_id,
        pre.power_ranking_entry_id
    FROM power_rankings pr
    JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
    JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
    WHERE pr.latest = 1
    ORDER BY pre.rank ASC
");

while($latest_power_ranking = $latest_power_rankings->fetch(PDO::FETCH_ASSOC)) {
    $power_ranking_entry_id = $latest_power_ranking['power_ranking_entry_id'];
    
    if($verbose_output) {
        $framework->coutLine("Caching power ranking ID {$power_ranking_entry_id}.");
    }
    
    $hash_name = "latest_power_rankings:{$power_ranking_entry_id}";

    foreach($latest_power_ranking as $column => $value) {
        $cache->hSet($hash_name, $column, $value);
    }
    
    $cache->rPush('latest_power_rankings_new', $hash_name);
}

if($current_rankings_exists) {
    $cache->rename('latest_power_rankings', 'latest_power_rankings_old');
}

$cache->rename('latest_power_rankings_new', 'latest_power_rankings');

$cache->set('latest_power_rankings_total_count', $cache->lSize('latest_power_rankings'));

if($verbose_output) {
    $framework->coutLine("Deleting old cached data.");
}

if($current_rankings_exists) {
    while($old_leadboard_entry = $cache->rPop('latest_power_rankings_old')) {
        if($verbose_output) {
            $framework->coutLine("Deleting power ranking ID {$old_leadboard_entry}.");
        }
    
        $cache->delete($old_leadboard_entry);
    }
    
    $cache->delete('latest_power_rankings_old');
}