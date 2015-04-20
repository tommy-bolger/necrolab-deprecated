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

$framework = new Framework('h', false);

if(isset($framework->arguments->h)) {
    display_help();
}

$module = new Module('necrolab');

$cache = cache();

/* ---------- Cache the latest power rankings ---------- */

$framework->coutLine("Working on power rankings.");

$latest_power_rankings = db()->prepareExecuteQuery("
    SELECT
        pre.rank,
        su.personaname,
        pre.score_rank,
        pre.score_rank_points_total,
        pre.speed_rank,
        pre.speed_rank_points_total,
        pre.deathless_score_rank,
        pre.deathless_score_rank_points_total,
        pre.total_points,
        pre.steam_user_id,
        pre.power_ranking_entry_id,
        su.twitch_username,
        su.twitter_username,
        su.website
    FROM power_rankings pr
    JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
    JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
    WHERE pr.latest = 1    
    ORDER BY pre.rank ASC
");

while($latest_power_ranking = $latest_power_rankings->fetch(PDO::FETCH_ASSOC)) {
    $power_ranking_entry_id = $latest_power_ranking['power_ranking_entry_id'];

    $framework->coutLine("Caching power ranking ID {$power_ranking_entry_id}.");
    
    $hash_name = "latest_power_rankings:{$power_ranking_entry_id}";
    
    $cache->hMset($hash_name, $latest_power_ranking);
    
    $cache->rPush('latest_power_rankings_new', $hash_name);
    
    //Add the latest power ranking entry id to the steam user in cache
    $cache->hSet("steam_users:{$latest_power_ranking['steam_user_id']}", 'latest_power_ranking_id', $power_ranking_entry_id);
}

$current_power_rankings_exists = $cache->exists('latest_power_rankings');

if($current_power_rankings_exists) {
    $cache->rename('latest_power_rankings', 'latest_power_rankings_old');
}

$cache->rename('latest_power_rankings_new', 'latest_power_rankings');

$cache->set('total_count', $cache->lSize('latest_power_rankings'), 'latest_power_rankings');

$framework->coutLine("Deleting old cached data.");

if($current_power_rankings_exists) {
    while($old_leadboard_entry = $cache->rPop('latest_power_rankings_old')) {
        $framework->coutLine("Deleting power ranking ID {$old_leadboard_entry}.");
    
        $cache->delete($old_leadboard_entry);
    }
    
    $cache->delete('latest_power_rankings_old');
}


/* ---------- Cache the latest score rankings ---------- */

$framework->coutLine("Working on score rankings.");

$latest_score_rankings = db()->prepareExecuteQuery("
    SELECT
        pre.score_rank,
        su.personaname,
        pre.cadence_score_rank,
        pre.bard_score_rank,
        pre.monk_score_rank,
        pre.aria_score_rank,
        pre.bolt_score_rank,
        pre.dove_score_rank,
        pre.eli_score_rank,
        pre.melody_score_rank,
        pre.dorian_score_rank,
        pre.all_score_rank,
        pre.story_score_rank,
        pre.score_rank_points_total,
        pre.steam_user_id,
        pre.power_ranking_entry_id,
        su.twitch_username,
        su.twitter_username,
        su.website
    FROM power_rankings pr
    JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
    JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
    WHERE pr.latest = 1   
        AND pre.score_rank IS NOT NULL
    ORDER BY pre.score_rank ASC
");

while($latest_score_ranking = $latest_score_rankings->fetch(PDO::FETCH_ASSOC)) {
    $power_ranking_entry_id = $latest_score_ranking['power_ranking_entry_id'];
    
    $framework->coutLine("Caching score ranking ID {$power_ranking_entry_id}.");
    
    $hash_name = "latest_score_rankings:{$power_ranking_entry_id}";
    
    $cache->hMset($hash_name, $latest_score_ranking);
    
    $cache->rPush('latest_score_rankings_new', $hash_name);
    
    //Add the latest power ranking entry id to the steam user in cache
    $cache->hSet("steam_users:{$latest_score_ranking['steam_user_id']}", 'latest_score_ranking_id', $power_ranking_entry_id);
}

$current_score_rankings_exists = $cache->exists('latest_score_rankings');

if($current_score_rankings_exists) {
    $cache->rename('latest_score_rankings', 'latest_score_rankings_old');
}

$cache->rename('latest_score_rankings_new', 'latest_score_rankings');

$cache->set('total_count', $cache->lSize('latest_score_rankings'), 'latest_score_rankings');

$framework->coutLine("Deleting old cached data.");

if($current_score_rankings_exists) {
    while($old_leadboard_entry = $cache->rPop('latest_score_rankings_old')) {
        $framework->coutLine("Deleting score ranking ID {$old_leadboard_entry}.");
    
        $cache->delete($old_leadboard_entry);
    }
    
    $cache->delete('latest_score_rankings_old');
}


/* ---------- Cache the latest speed rankings ---------- */

$framework->coutLine("Working on speed rankings.");

$latest_speed_rankings = db()->prepareExecuteQuery("
    SELECT
        pre.speed_rank,
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
        pre.all_speed_rank,
        pre.story_speed_rank,
        pre.speed_rank_points_total,
        pre.steam_user_id,
        pre.power_ranking_entry_id,
        su.twitch_username,
        su.twitter_username,
        su.website
    FROM power_rankings pr
    JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
    JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
    WHERE pr.latest = 1   
        AND pre.speed_rank IS NOT NULL
    ORDER BY pre.speed_rank ASC
");

while($latest_speed_ranking = $latest_speed_rankings->fetch(PDO::FETCH_ASSOC)) {
    $power_ranking_entry_id = $latest_speed_ranking['power_ranking_entry_id'];

    $framework->coutLine("Caching speed ranking ID {$power_ranking_entry_id}.");
    
    $hash_name = "latest_speed_rankings:{$power_ranking_entry_id}";
    
    $cache->hMset($hash_name, $latest_speed_ranking);
    
    $cache->rPush('latest_speed_rankings_new', $hash_name);
    
    //Add the latest power ranking entry id to the steam user in cache
    $cache->hSet("steam_users:{$latest_speed_ranking['steam_user_id']}", 'latest_speed_ranking_id', $power_ranking_entry_id);
}

$current_speed_rankings_exists = $cache->exists('latest_speed_rankings');

if($current_speed_rankings_exists) {
    $cache->rename('latest_speed_rankings', 'latest_speed_rankings_old');
}

$cache->rename('latest_speed_rankings_new', 'latest_speed_rankings');

$cache->set('total_count', $cache->lSize('latest_speed_rankings'), 'latest_speed_rankings');

$framework->coutLine("Deleting old cached data.");

if($current_speed_rankings_exists) {
    while($old_leadboard_entry = $cache->rPop('latest_speed_rankings_old')) {
        $framework->coutLine("Deleting speed ranking ID {$old_leadboard_entry}.");
    
        $cache->delete($old_leadboard_entry);
    }
    
    $cache->delete('latest_speed_rankings_old');
}


/* ---------- Cache the latest deathless score rankings ---------- */

$framework->coutLine("Working on deathless score rankings.");

$latest_deathless_score_rankings = db()->prepareExecuteQuery("
    SELECT
        pre.deathless_score_rank,
        su.personaname,
        pre.cadence_deathless_score_rank,
        pre.bard_deathless_score_rank,
        pre.monk_deathless_score_rank,
        pre.aria_deathless_score_rank,
        pre.bolt_deathless_score_rank,
        pre.dove_deathless_score_rank,
        pre.eli_deathless_score_rank,
        pre.melody_deathless_score_rank,
        pre.dorian_deathless_score_rank,
        pre.deathless_score_rank_points_total,
        pre.steam_user_id,
        pre.power_ranking_entry_id,
        su.twitch_username,
        su.twitter_username,
        su.website
    FROM power_rankings pr
    JOIN power_ranking_entries pre ON pre.power_ranking_id = pr.power_ranking_id
    JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
    WHERE pr.latest = 1   
        AND pre.deathless_score_rank IS NOT NULL
    ORDER BY pre.deathless_score_rank ASC
");

while($latest_deathless_score_ranking = $latest_deathless_score_rankings->fetch(PDO::FETCH_ASSOC)) {
    $power_ranking_entry_id = $latest_deathless_score_ranking['power_ranking_entry_id'];
    
    $framework->coutLine("Caching deathless score ranking ID {$power_ranking_entry_id}.");
    
    $hash_name = "latest_deathless_score_rankings:{$power_ranking_entry_id}";
    
    $cache->hMset($hash_name, $latest_deathless_score_ranking);
    
    $cache->rPush('latest_deathless_score_rankings_new', $hash_name);
    
    //Add the latest power ranking entry id to the steam user in cache
    $cache->hSet("steam_users:{$latest_deathless_score_ranking['steam_user_id']}", 'latest_deathless_score_id', $power_ranking_entry_id);
}

$current_deathless_score_rankings_exists = $cache->exists('latest_deathless_score_rankings');

if($current_deathless_score_rankings_exists) {
    $cache->rename('latest_deathless_score_rankings', 'latest_deathless_score_rankings_old');
}

$cache->rename('latest_deathless_score_rankings_new', 'latest_deathless_score_rankings');

$cache->set('total_count', $cache->lSize('latest_deathless_score_rankings'), 'latest_deathless_score_rankings');

$framework->coutLine("Deleting old cached data.");

if($current_deathless_score_rankings_exists) {
    while($old_leadboard_entry = $cache->rPop('latest_deathless_score_rankings_old')) {
        $framework->coutLine("Deleting deathless score ranking ID {$old_leadboard_entry}.");
    
        $cache->delete($old_leadboard_entry);
    }
    
    $cache->delete('latest_deathless_score_rankings_old');
}