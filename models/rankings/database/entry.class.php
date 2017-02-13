<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Modules\Necrolab\Models\Rankings\Database\RecordModels\PowerRankingEntry as DatabaseEntry;
use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
use \Modules\Necrolab\Models\Rankings\Entry as BaseEntry;

class Entry
extends BaseEntry {
    public static function save(DateTime $date, DatabaseEntry $database_entry) {
        $date_formatted = $date->format('Y_m');
    
        db()->insert("power_ranking_entries_{$date_formatted}", $database_entry->toArray(), "power_ranking_entry_{$date_formatted}_insert", false);
    }
    
    public static function setSelectFields($resultset) {
        $active_characters = DatabaseCharacters::getActive();
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'pre.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'pre.power_ranking_id',
                'alias' => 'power_ranking_id'
            )
        ));
        
        if(!empty($active_characters)) {
            foreach($active_characters as $active_character) {
                $character_name = $active_character['name'];
            
                $resultset->addSelectFields(array(
                    array(
                        'field' => "pre.{$character_name}_score_rank",
                        'alias' => "{$character_name}_score_rank",
                    ),
                    array(
                        'field' => "pre.{$character_name}_score_rank_points",
                        'alias' => "{$character_name}_score_rank_points",
                    ),
                    array(
                        'field' => "pre.{$character_name}_score",
                        'alias' => "{$character_name}_score",
                    ),
                    array(
                        'field' => "pre.{$character_name}_speed_rank",
                        'alias' => "{$character_name}_speed_rank",
                    ),
                    array(
                        'field' => "pre.{$character_name}_speed_rank_points",
                        'alias' => "{$character_name}_speed_rank_points",
                    ),
                    array(
                        'field' => "pre.cadence_speed_time",
                        'alias' => "{$character_name}_speed_time",
                    ),
                    array(
                        'field' => "pre.{$character_name}_rank",
                        'alias' => "{$character_name}_rank",
                    ),
                    array(
                        'field' => "pre.{$character_name}_rank_points",
                        'alias' => "{$character_name}_rank_points",
                    )
                ));
                
                if($character_name != 'all' && $character_name != 'story') {
                    $resultset->addSelectFields(array(
                        array(
                            'field' => "pre.{$character_name}_deathless_rank",
                            'alias' => "{$character_name}_deathless_rank",
                        ),
                        array(
                            'field' => "pre.{$character_name}_deathless_rank_points",
                            'alias' => "{$character_name}_deathless_rank_points",
                        ),
                        array(
                            'field' => "pre.{$character_name}_deathless_win_count",
                            'alias' => "{$character_name}_deathless_win_count",
                        ),
                    ));
                }
            }
        }
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'pre.score_total',
                'alias' => 'score_total',
            ),
            array(
                'field' => 'pre.score_rank',
                'alias' => 'score_rank',
            ),
            array(
                'field' => 'pre.score_rank_points_total',
                'alias' => 'score_rank_points_total',
            ),
            array(
                'field' => 'pre.deathless_total_win_count',
                'alias' => 'deathless_total_win_count',
            ),
            array(
                'field' => 'pre.deathless_rank',
                'alias' => 'deathless_rank',
            ),
            array(
                'field' => 'pre.deathless_rank_points_total',
                'alias' => 'deathless_rank_points_total',
            ),
            array(
                'field' => 'pre.speed_total_time',
                'alias' => 'speed_total_time',
            ),
            array(
                'field' => 'pre.speed_rank',
                'alias' => 'speed_rank',
            ),
            array(
                'field' => 'pre.speed_rank_points_total',
                'alias' => 'speed_rank_points_total',
            ),
            array(
                'field' => 'pre.rank',
                'alias' => 'rank',
            ),
            array(
                'field' => 'pre.total_points',
                'alias' => 'total_points',
            )
        ));
    }
}