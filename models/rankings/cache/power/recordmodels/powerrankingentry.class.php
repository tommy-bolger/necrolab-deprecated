<?php

namespace Modules\Necrolab\Models\Rankings\Cache\Power\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class PowerRankingEntry
extends RecordModel {    
    protected $steamid;

    protected $cadence_score_rank;

    protected $cadence_score_rank_points;

    protected $cadence_score;

    protected $bard_score_rank;

    protected $bard_score_rank_points;

    protected $bard_score;

    protected $monk_score_rank;

    protected $monk_score_rank_points;

    protected $monk_score;

    protected $aria_score_rank;

    protected $aria_score_rank_points;

    protected $aria_score;

    protected $bolt_score_rank;

    protected $bolt_score_rank_points;

    protected $bolt_score;

    protected $dove_score_rank;

    protected $dove_score_rank_points;

    protected $dove_score;

    protected $eli_score_rank;

    protected $eli_score_rank_points;

    protected $eli_score;

    protected $melody_score_rank;

    protected $melody_score_rank_points;

    protected $melody_score;

    protected $dorian_score_rank;

    protected $dorian_score_rank_points;

    protected $dorian_score;

    protected $coda_score_rank;

    protected $coda_score_rank_points;

    protected $coda_score;

    protected $all_score_rank;

    protected $all_score_rank_points;

    protected $all_score;

    protected $story_score_rank;

    protected $story_score_rank_points;

    protected $story_score;

    protected $score_rank_points_total;

    protected $cadence_deathless_score_rank;

    protected $cadence_deathless_score_rank_points;

    protected $cadence_deathless_score;

    protected $bard_deathless_score_rank;

    protected $bard_deathless_score_rank_points;

    protected $bard_deathless_score;

    protected $monk_deathless_score_rank;

    protected $monk_deathless_score_rank_points;

    protected $monk_deathless_score;

    protected $aria_deathless_score_rank;

    protected $aria_deathless_score_rank_points;

    protected $aria_deathless_score;

    protected $bolt_deathless_score_rank;

    protected $bolt_deathless_score_rank_points;

    protected $bolt_deathless_score;

    protected $dove_deathless_score_rank;

    protected $dove_deathless_score_rank_points;

    protected $dove_deathless_score;

    protected $eli_deathless_score_rank;

    protected $eli_deathless_score_rank_points;

    protected $eli_deathless_score;

    protected $melody_deathless_score_rank;

    protected $melody_deathless_score_rank_points;

    protected $melody_deathless_score;

    protected $dorian_deathless_score_rank;

    protected $dorian_deathless_score_rank_points;

    protected $dorian_deathless_score;

    protected $coda_deathless_score_rank;

    protected $coda_deathless_score_rank_points;

    protected $coda_deathless_score;

    protected $all_deathless_score_rank;

    protected $all_deathless_score_rank_points;

    protected $all_deathless_score;

    protected $story_deathless_score_rank;

    protected $story_deathless_score_rank_points;

    protected $story_deathless_score;

    protected $deathless_score_rank_points_total;

    protected $cadence_speed_rank;

    protected $cadence_speed_rank_points;

    protected $cadence_speed_time;

    protected $bard_speed_rank;

    protected $bard_speed_rank_points;

    protected $bard_speed_time;

    protected $monk_speed_rank;

    protected $monk_speed_rank_points;

    protected $monk_speed_time;

    protected $aria_speed_rank;

    protected $aria_speed_rank_points;

    protected $aria_speed_time;

    protected $bolt_speed_rank;

    protected $bolt_speed_rank_points;

    protected $bolt_speed_time;

    protected $dove_speed_rank;

    protected $dove_speed_rank_points;

    protected $dove_speed_time;

    protected $eli_speed_rank;

    protected $eli_speed_rank_points;

    protected $eli_speed_time;

    protected $melody_speed_rank;

    protected $melody_speed_rank_points;

    protected $melody_speed_time;

    protected $dorian_speed_rank;

    protected $dorian_speed_rank_points;

    protected $dorian_speed_time;

    protected $coda_speed_rank;

    protected $coda_speed_rank_points;

    protected $coda_speed_time;

    protected $all_speed_rank;

    protected $all_speed_rank_points;

    protected $all_speed_time;

    protected $story_speed_rank;

    protected $story_speed_rank_points;

    protected $story_speed_time;

    protected $speed_rank_points_total;

    protected $total_points;

    protected $speed_rank;

    protected $score_rank;

    protected $deathless_score_rank;

    protected $rank;
    
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
            case 'cadence_deathless_score_rank':
            case 'cadence_deathless_score_time':
            case 'bard_deathless_score_rank':
            case 'bard_deathless_score_time':
            case 'monk_deathless_score_rank':
            case 'monk_deathless_score_time':
            case 'aria_deathless_score_rank':
            case 'aria_deathless_score_time':
            case 'bolt_deathless_score_rank':
            case 'bolt_deathless_score_time':
            case 'dove_deathless_score_rank':
            case 'dove_deathless_score_time':
            case 'eli_deathless_score_rank':
            case 'eli_deathless_score_time':
            case 'melody_deathless_score_rank':
            case 'melody_deathless_score_time':
            case 'dorian_deathless_score_rank':
            case 'dorian_deathless_score_time':
            case 'coda_deathless_score_rank':
            case 'coda_deathless_score_time':
            case 'deathless_score_rank':
            case 'cadence_speed_rank':
            case 'cadence_speed_time':
            case 'bard_speed_rank':
            case 'bard_speed_time':
            case 'monk_speed_rank':
            case 'monk_speed_time':
            case 'aria_speed_rank':
            case 'aria_speed_time':
            case 'bolt_speed_rank':
            case 'bolt_speed_time':
            case 'dove_speed_rank':
            case 'dove_speed_time':
            case 'eli_speed_rank':
            case 'eli_speed_time':
            case 'melody_speed_rank':
            case 'melody_speed_time':
            case 'dorian_speed_rank':
            case 'dorian_speed_time':
            case 'coda_speed_rank':
            case 'coda_speed_time':
            case 'all_speed_rank':
            case 'all_speed_time':
            case 'story_speed_rank':
            case 'story_speed_time':
            case 'speed_rank':
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
            case 'cadence_deathless_score_rank_points':
            case 'bard_deathless_score_rank_points':
            case 'monk_deathless_score_rank_points':
            case 'aria_deathless_score_rank_points':
            case 'bolt_deathless_score_rank_points':
            case 'dove_deathless_score_rank_points':
            case 'eli_deathless_score_rank_points':
            case 'melody_deathless_score_rank_points':
            case 'dorian_deathless_score_rank_points':
            case 'coda_deathless_score_rank_points':
            case 'deathless_score_rank_points_total':
            case 'cadence_speed_rank_points':
            case 'bard_speed_rank_points':
            case 'monk_speed_rank_points':
            case 'aria_speed_rank_points':
            case 'bolt_speed_rank_points':
            case 'dove_speed_rank_points':
            case 'eli_speed_rank_points':
            case 'melody_speed_rank_points':
            case 'dorian_speed_rank_points':
            case 'coda_speed_rank_points':
            case 'all_speed_rank_points':
            case 'story_speed_rank_points':
            case 'speed_rank_points_total':
                $new_property_value = (float)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
}