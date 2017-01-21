<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \DateTime;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\RecordModels\DailyRankingEntry as DatabaseDailyRankingEntry;

class Entry {
    public static function save(DateTime $date, DatabaseDailyRankingEntry $database_entry) {
        $date_formatted = $date->format('Y_m');
    
        db()->insert("daily_ranking_entries_{$date_formatted}", $database_entry->toArray(), "daily_ranking_entries_{$date_formatted}_insert", false);
    }
}