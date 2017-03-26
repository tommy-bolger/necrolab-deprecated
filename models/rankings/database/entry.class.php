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
            
                $score_pb_name = "{$character_name}_score_pb";
                $speed_pb_name = "{$character_name}_speed_pb";
            
                $resultset->addLeftJoinCriteria("steam_user_pbs {$score_pb_name} ON {$score_pb_name}.steam_user_pb_id = pre.{$score_pb_name}_id");
                $resultset->addLeftJoinCriteria("steam_user_pbs {$speed_pb_name} ON {$speed_pb_name}.steam_user_pb_id = pre.{$speed_pb_name}_id");
            
                $resultset->addSelectFields(array(
                    array(
                        'field' => "pre.{$character_name}_score_rank",
                        'alias' => "{$character_name}_score_rank",
                    ),
                    array(
                        'field' => "{$score_pb_name}.score",
                        'alias' => "{$character_name}_score",
                    ),
                    array(
                        'field' => "pre.{$character_name}_speed_rank",
                        'alias' => "{$character_name}_speed_rank",
                    ),
                    array(
                        'field' => "{$speed_pb_name}.time",
                        'alias' => "{$character_name}_speed_time",
                    ),
                    array(
                        'field' => "pre.{$character_name}_rank",
                        'alias' => "{$character_name}_rank",
                    )
                ));
                
                if($character_name != 'all' && $character_name != 'story') {
                    $deathless_pb_name = "{$character_name}_deathless_pb";
                    
                    $resultset->addLeftJoinCriteria("steam_user_pbs {$deathless_pb_name} ON {$deathless_pb_name}.steam_user_pb_id = pre.{$deathless_pb_name}_id");
                
                    $resultset->addSelectFields(array(
                        array(
                            'field' => "pre.{$character_name}_deathless_rank",
                            'alias' => "{$character_name}_deathless_rank",
                        ),
                        array(
                            'field' => "{$deathless_pb_name}.win_count",
                            'alias' => "{$character_name}_deathless_win_count",
                        ),
                    ));
                }
            }
        }
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'pre.score_rank',
                'alias' => 'score_rank',
            ),
            array(
                'field' => 'pre.deathless_rank',
                'alias' => 'deathless_rank',
            ),
            array(
                'field' => 'pre.speed_rank',
                'alias' => 'speed_rank',
            ),
            array(
                'field' => 'pre.rank',
                'alias' => 'rank',
            )
        ));
    }
}