<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \DateTime;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\RecordModels\DailyRankingEntry as DatabaseDailyRankingEntry;
use \Modules\Necrolab\Models\Dailies\Rankings\Entry as BaseEntry;

class Entry
extends BaseEntry {
    public static function save(DateTime $date, DatabaseDailyRankingEntry $database_entry) {
        $date_formatted = $date->format('Y_m');
    
        db()->insert("daily_ranking_entries_{$date_formatted}", $database_entry->toArray(), "daily_ranking_entries_{$date_formatted}_insert", false);
    }
    
    public static function setSelectFields($resultset) {   
        $resultset->addSelectFields(array(
            array(
                'field' => 'dre.rank',
                'alias' => 'rank',
            ),
            array(
                'field' => 'dre.first_place_ranks',
                'alias' => 'first_place_ranks',
            ),
            array(
                'field' => 'dre.top_5_ranks',
                'alias' => 'top_5_ranks',
            ),
            array(
                'field' => 'dre.top_10_ranks',
                'alias' => 'top_10_ranks',
            ),
            array(
                'field' => 'dre.top_20_ranks',
                'alias' => 'top_20_ranks',
            ),
            array(
                'field' => 'dre.top_50_ranks',
                'alias' => 'top_50_ranks',
            ),
            array(
                'field' => 'dre.top_100_ranks',
                'alias' => 'top_100_ranks',
            ),
            array(
                'field' => 'dre.total_points',
                'alias' => 'total_points',
            ),
            array(
                'field' => 'dre.total_score',
                'alias' => 'total_score',
            ),
            array(
                'field' => 'dre.total_dailies',
                'alias' => 'total_dailies',
            ),
            array(
                'field' => 'dre.total_wins',
                'alias' => 'total_wins',
            ),
            array(
                'field' => 'dre.sum_of_ranks',
                'alias' => 'sum_of_ranks',
            ),
            array(
                'field' => 'dre.steam_user_id',
                'alias' => 'steam_user_id',
            ),
        ));
    }
}