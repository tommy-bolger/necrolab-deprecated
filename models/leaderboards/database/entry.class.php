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
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'le.steam_user_id',
                'alias' => 'steam_user_id',
            ),
            array(
                'field' => 'le.score',
                'alias' => 'score',
            ),
            array(
                'field' => 'le.time',
                'alias' => 'time',
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank',
            ),
            array(
                'field' => 'le.is_win',
                'alias' => 'is_win',
            ),
            array(
                'field' => 'le.zone',
                'alias' => 'zone',
            ),
            array(
                'field' => 'le.level',
                'alias' => 'level',
            ),
            array(
                'field' => 'le.win_count',
                'alias' => 'win_count',
            )
        ));
    }
}