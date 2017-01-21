<?php

namespace Modules\Necrolab\Models\Rankings\Cache\Score\RecordModels;

use \Framework\Core\RecordModel;

class ScoreEntry
extends RecordModel {    
    protected $steamid;

    protected $cadence_score_rank;

    protected $cadence_score_rank_points;

    protected $cadence_score_time;

    protected $bard_score_rank;

    protected $bard_score_rank_points;

    protected $bard_score_time;

    protected $monk_score_rank;

    protected $monk_score_rank_points;

    protected $monk_score_time;

    protected $aria_score_rank;

    protected $aria_score_rank_points;

    protected $aria_score_time;

    protected $bolt_score_rank;

    protected $bolt_score_rank_points;

    protected $bolt_score_time;

    protected $dove_score_rank;

    protected $dove_score_rank_points;

    protected $dove_score_time;

    protected $eli_score_rank;

    protected $eli_score_rank_points;

    protected $eli_score_time;

    protected $melody_score_rank;

    protected $melody_score_rank_points;

    protected $melody_score_time;

    protected $dorian_score_rank;

    protected $dorian_score_rank_points;

    protected $dorian_score_time;

    protected $coda_score_rank;

    protected $coda_score_rank_points;

    protected $coda_score_time;

    protected $all_score_rank;

    protected $all_score_rank_points;

    protected $all_score_time;

    protected $story_score_rank;

    protected $story_score_rank_points;

    protected $story_score_time;

    protected $score_rank_points_total;

    protected $score_rank;
    
    protected function getPropertyValue($property_name, $property_value) { 
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'steamid':
            case 'cadence_score_rank':
            case 'cadence_score_time':
            case 'bard_score_rank':
            case 'bard_score_time':
            case 'monk_score_rank':
            case 'monk_score_time':
            case 'aria_score_rank':
            case 'aria_score_time':
            case 'bolt_score_rank':
            case 'bolt_score_time':
            case 'dove_score_rank':
            case 'dove_score_time':
            case 'eli_score_rank':
            case 'eli_score_time':
            case 'melody_score_rank':
            case 'melody_score_time':
            case 'dorian_score_rank':
            case 'dorian_score_time':
            case 'coda_score_rank':
            case 'coda_score_time':
            case 'all_score_rank':
            case 'all_score_time':
            case 'story_score_rank':
            case 'story_score_time':
            case 'score_rank':
                $new_property_value = (int)$property_value;
                break;
            case 'cadence_score_rank_points':
            case 'bard_score_rank_points':
            case 'monk_score_rank_points':
            case 'aria_score_rank_points':
            case 'bolt_score_rank_points':
            case 'dove_score_rank_points':
            case 'eli_score_rank_points':
            case 'melody_score_rank_points':
            case 'dorian_score_rank_points':
            case 'coda_score_rank_points':
            case 'all_score_rank_points':
            case 'story_score_rank_points':
            case 'score_rank_points_total':
                $new_property_value = (float)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
}