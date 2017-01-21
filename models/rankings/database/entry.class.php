<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Modules\Necrolab\Models\Rankings\Database\RecordModels\PowerRankingEntry as DatabaseEntry;

class Entry {
    public static function save(DateTime $date, DatabaseEntry $database_entry) {
        $date_formatted = $date->format('Y_m');
    
        db()->insert("power_ranking_entries_{$date_formatted}", $database_entry->toArray(), "power_ranking_entry_{$date_formatted}_insert", false);
    }
}