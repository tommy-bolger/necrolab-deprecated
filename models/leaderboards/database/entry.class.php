<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Leaderboards\Entry as BaseEntry;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\LeaderboardEntry as DatabaseEntry;

class Entry
extends BaseEntry {
    public static function save(DateTime $date, DatabaseEntry $database_entry) {
        $date_formatted = $date->format('Y_m');
    
        db()->insert("leaderboard_entries_{$date_formatted}", $database_entry->toArray(), "leaderboard_entries_{$date_formatted}_insert", false);
    }
}