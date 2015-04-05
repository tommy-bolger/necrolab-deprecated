<?php
/* ---------- Leaderboard Entries Cleanup ---------- */

$leaderboard_entries = db()->prepareExecuteQuery("
    SELECT
        leaderboard_snapshot_id,
        leaderboard_id,
        created
    FROM leaderboard_snapshots
    ORDER BY leaderboard_id, created
");

$leaderboard_snapshot_ids_to_keep = array();

$leaderboard_snapshot_ids_to_delete = array();

while($leaderboard_entry = $leaderboard_entries->fetch(PDO::FETCH_ASSOC)) {
    $leaderboard_id = $leaderboard_entry['leaderboard_id'];
    $date = date('Y-m-d', strtotime($leaderboard_entry['created']));

    if(!empty($leaderboard_snapshot_ids_to_keep[$leaderboard_id][$date])) {
        $leaderboard_snapshot_ids_to_delete[] = $leaderboard_snapshot_ids_to_keep[$leaderboard_id][$date];
    }
    
    $leaderboard_snapshot_ids_to_keep[$leaderboard_id][$date] = $leaderboard_entry['leaderboard_snapshot_id'];
}

if(!empty($leaderboard_snapshot_ids_to_delete)) {
    $leaderboard_snapshot_ids = implode(',', $leaderboard_snapshot_ids_to_delete);
    
    db()->query("
        DELETE FROM leaderboard_snapshots
        WHERE leaderboard_snapshot_id IN ({$leaderboard_snapshot_ids})
    ");
    
    db()->query("
        DELETE FROM leaderboard_entries
        WHERE leaderboard_snapshot_id IN ({$leaderboard_snapshot_ids})
    ");
}

if(!empty($leaderboard_snapshot_ids_to_keep)) {
    foreach($leaderboard_snapshot_ids_to_keep as $leaderboard_id => $leaderboard_snapshot_id_to_keep) {
        foreach($leaderboard_snapshot_id_to_keep as $date => $leaderboard_snapshot_id) {
            db()->update('leaderboard_snapshots', array(
                'date' => $date
            ), array(
                'leaderboard_snapshot_id' => $leaderboard_snapshot_id
            ));
        }
    }
}

/* ---------- Power Rankings Cleanup ---------- */

$power_ranking_entries = db()->prepareExecuteQuery("
    SELECT
        power_ranking_id,
        created
    FROM power_rankings
    ORDER BY created
");

$power_ranking_ids_to_keep = array();

$power_ranking_ids_to_delete = array();

while($power_ranking_entry = $power_ranking_entries->fetch(PDO::FETCH_ASSOC)) {
    $power_ranking_id = $power_ranking_entry['power_ranking_id'];
    $date = date('Y-m-d', strtotime($power_ranking_entry['created']));

    if(!empty($power_ranking_ids_to_keep[$date])) {
        $power_ranking_ids_to_delete[] = $power_ranking_ids_to_keep[$date];
    }
    
    $power_ranking_ids_to_keep[$date] = $power_ranking_id;
}

if(!empty($power_ranking_ids_to_delete)) {
    $power_ranking_ids = implode(',', $power_ranking_ids_to_delete);
    
    db()->query("
        DELETE FROM power_rankings
        WHERE power_ranking_id IN ({$power_ranking_ids})
    ");
    
    db()->query("
        DELETE FROM power_ranking_leaderboard_snapshots
        WHERE power_ranking_id IN ({$power_ranking_ids})
    ");
    
    db()->query("
        DELETE FROM power_ranking_entries
        WHERE power_ranking_id IN ({$power_ranking_ids})
    ");
}

if(!empty($power_ranking_ids_to_keep)) {
    foreach($power_ranking_ids_to_keep as $date => $power_ranking_id) {
        db()->update('power_rankings', array(
            'date' => $date
        ), array(
            'power_ranking_id' => $power_ranking_id
        ));
    }
}

/* ---------- Daily Rankings Cleanup ---------- */

$daily_ranking_entries = db()->prepareExecuteQuery("
    SELECT
        daily_ranking_id,
        created
    FROM daily_rankings
    ORDER BY created
");

$daily_ranking_ids_to_keep = array();

$daily_ranking_ids_to_delete = array();

while($daily_ranking_entry = $daily_ranking_entries->fetch(PDO::FETCH_ASSOC)) {
    $daily_ranking_id = $daily_ranking_entry['daily_ranking_id'];
    $date = date('Y-m-d', strtotime($daily_ranking_entry['created']));

    if(!empty($daily_ranking_ids_to_keep[$date])) {
        $daily_ranking_ids_to_delete[] = $daily_ranking_ids_to_keep[$date];
    }
    
    $daily_ranking_ids_to_keep[$date] = $daily_ranking_id;
}

if(!empty($daily_ranking_ids_to_delete)) {
    $daily_ranking_ids = implode(',', $daily_ranking_ids_to_delete);
    
    db()->query("
        DELETE FROM daily_rankings
        WHERE daily_ranking_id IN ({$daily_ranking_ids})
    ");
    
    db()->query("
        DELETE FROM daily_ranking_leaderboard_snapshots
        WHERE daily_ranking_id IN ({$daily_ranking_ids})
    ");
    
    db()->query("
        DELETE FROM daily_ranking_entries
        WHERE daily_ranking_id IN ({$daily_ranking_ids})
    ");
}

if(!empty($daily_ranking_ids_to_keep)) {
    foreach($daily_ranking_ids_to_keep as $date => $daily_ranking_id) {
        db()->update('daily_rankings', array(
            'date' => $date
        ), array(
            'daily_ranking_id' => $daily_ranking_id
        ));
    }
}