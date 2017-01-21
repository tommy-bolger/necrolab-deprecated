<?php

namespace Modules\Necrolab\Models\Rankings\Cache\Deathless\RecordModels;

use \Framework\Core\RecordModel;

class DeathlessEntry
extends RecordModel {    
    protected $steamid;

    protected $cadence_deathless_score_rank;

    protected $cadence_deathless_score_rank_points;

    protected $cadence_deathless_score_time;

    protected $bard_deathless_score_rank;

    protected $bard_deathless_score_rank_points;

    protected $bard_deathless_score_time;

    protected $monk_deathless_score_rank;

    protected $monk_deathless_score_rank_points;

    protected $monk_deathless_score_time;

    protected $aria_deathless_score_rank;

    protected $aria_deathless_score_rank_points;

    protected $aria_deathless_score_time;

    protected $bolt_deathless_score_rank;

    protected $bolt_deathless_score_rank_points;

    protected $bolt_deathless_score_time;

    protected $dove_deathless_score_rank;

    protected $dove_deathless_score_rank_points;

    protected $dove_deathless_score_time;

    protected $eli_deathless_score_rank;

    protected $eli_deathless_score_rank_points;

    protected $eli_deathless_score_time;

    protected $melody_deathless_score_rank;

    protected $melody_deathless_score_rank_points;

    protected $melody_deathless_score_time;

    protected $dorian_deathless_score_rank;

    protected $dorian_deathless_score_rank_points;

    protected $dorian_deathless_score_time;

    protected $coda_deathless_score_rank;

    protected $coda_deathless_score_rank_points;

    protected $coda_deathless_score_time;

    protected $deathless_score_rank_points_total;

    protected $deathless_score_rank;
    
    protected function getPropertyValue($property_name, $property_value) { 
        $new_property_value = NULL;
    
        switch($property_name) {
            case 'steamid':
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
                $new_property_value = (int)$property_value;
                break;
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
                $new_property_value = (float)$property_value;
                break;
            default:
                $new_property_value = $property_value;
                break;
        }
        
        return $new_property_value;
    }
}