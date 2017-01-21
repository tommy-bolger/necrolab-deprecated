<?php
use \Modules\Necrolab\Models\Leaderboards\Leaderboards;

$leaderboard_entries = db()->prepareExecuteQuery("
    SELECT 
        leaderboard_entry_id,
        details
    FROM leaderboard_entries
");

while($leaderboard_entry = $leaderboard_entries->fetch(PDO::FETCH_ASSOC)) {
    $leaderboard_entry_id = $leaderboard_entry['leaderboard_entry_id'];
    
    $zone_level = Leaderboards::getHighestZoneLevelFromDetails($leaderboard_entry['details']);

    db()->update('leaderboard_entries', array(
        'zone' => $zone_level['highest_zone'],
        'level' => $zone_level['highest_level']
    ), array(
        'leaderboard_entry_id' => $leaderboard_entry_id
    ));
}