<?php

namespace Modules\Necrolab\Models\Leaderboards\Database\RecordModels;

use \Exception;
use \DateTime;
use \Framework\Core\RecordModel;
use \Framework\Modules\Module;
use \Modules\Necrolab\Models\Leaderboards\Database\Blacklist;
use \Modules\Necrolab\Models\Releases;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\Characters;

class Leaderboard
extends RecordModel {  
    protected $name;
    
    protected $url;
    
    protected $lbid;
    
    protected $display_name;
    
    protected $character_id;
    
    protected $is_speedrun;
    
    protected $is_custom;
    
    protected $is_co_op;
    
    protected $is_seeded;
    
    protected $is_daily;
    
    protected $daily_date;
    
    protected $is_score_run;
    
    protected $is_deathless;

    protected $is_dev;
    
    protected $is_prod;
    
    protected $is_power_ranking;
    
    protected $is_daily_ranking; 
    
    protected $release_id;
    
    protected $mode_id;
    
    protected function getPropertyValue($property_name, $property_value) {         
        return $property_value;
    }
    
    public function setPropertiesFromObject($leaderboard, $error_on_invalid_property = false) {    
        $leaderboard_name = strtolower($leaderboard->name);
        $character_name = '';
        
        /*
            Retrieve which character this leaderboard is for.
            This is done by looking for a case insensitive version of each charcter's name.
            This is sloppy, but the only way I could find to fairly reliably get the character of each leaderboard with the data given.
        */
        if(strpos($leaderboard_name, 'bard') !== false) {
            $character_name = 'bard';
        }
        elseif(strpos($leaderboard_name, 'aria') !== false) {
            $character_name = 'aria';
        }
        elseif(strpos($leaderboard_name, 'monk') !== false) {
            $character_name = 'monk';
        }
        elseif(strpos($leaderboard_name, 'bolt') !== false) {
            $character_name = 'bolt';
        }
        elseif(strpos($leaderboard_name, 'dove') !== false) {
            $character_name = 'dove';
        }
        elseif(strpos($leaderboard_name, 'eli') !== false) {
            $character_name = 'eli';
        }
        elseif(strpos($leaderboard_name, 'melody') !== false) {
            $character_name = 'melody';
        }
        elseif(strpos($leaderboard_name, 'dorian') !== false) {
            $character_name = 'dorian';
        }
        elseif(strpos($leaderboard_name, 'coda') !== false) {
            $character_name = 'coda';
        }
        elseif(strpos($leaderboard_name, 'ghost') !== false) {
            $character_name = 'ghost';
        }
        elseif(strpos($leaderboard_name, 'pacifist') !== false) {
            $character_name = 'pacifist';
        }
        elseif(strpos($leaderboard_name, 'thief') !== false) {
            $character_name = 'thief';
        }
        elseif(strpos($leaderboard_name, 'nocturna') !== false) {
            $character_name = 'nocturna';
        }
        elseif(strpos($leaderboard_name, 'diamond') !== false) {
            $character_name = 'diamond';
        }
        elseif(strpos($leaderboard_name, 'mary') !== false) {
            $character_name = 'mary';
        }
        elseif(strpos($leaderboard_name, 'tempo') !== false) {
            $character_name = 'tempo';
        }
        //If nobody else assume it's Cadence
        else {
            $character_name = 'cadence';
        }
        
        $is_speedrun = 0;
        $is_custom = 0;
        $is_co_op = 0;
        $is_seeded = 0;
        $is_daily = 0;
        $is_score_run = 0;
        $is_deathless = 0;
        $is_dev = 0;
        $is_prod = 0;
        $is_dlc = 0;
        $mode = Modes::getByName('normal');
        
        if(strpos($leaderboard_name, 'speedrun') !== false) {
            $is_speedrun = 1;
            $is_score_run = 0;
        }
        
        if(strpos($leaderboard_name, 'custom') !== false) {
            $is_custom = 1;
        }
        
        if(strpos($leaderboard_name, 'co-op') !== false) {
            $is_co_op = 1;
        }
        
        if(strpos($leaderboard_name, 'seeded') !== false) {
            $is_seeded = 1;
        }
        
        if(strpos($leaderboard_name, 'hardcore') !== false || strpos($leaderboard_name, 'core') !== false || strpos($leaderboard_name, 'all zones') !== false) {
            $is_speedrun = 0;
            $is_score_run = 1;
        }
        
        if(strpos($leaderboard_name, 'all chars dlc') !== false) {
            $character_name = 'all_alc';
        }
        
        if(strpos($leaderboard_name, 'all chars') !== false) {
            $character_name = 'all';
        }
        
        if(strpos($leaderboard_name, 'deathless') !== false) {
            $is_deathless = 1;
        }
        
        if(strpos($leaderboard_name, 'story') !== false) {
            $character_name = 'story';
        }
        
        if(strpos($leaderboard_name, 'dev') !== false) {
            $is_dev = 1;
        }
        
        if(strpos($leaderboard_name, 'prod') !== false) {
            $is_prod = 1;
        }
        
        if(strpos($leaderboard_name, 'dlc') !== false) {
            $is_dlc = 1;
        }
        
        if(strpos($leaderboard_name, 'hard mode') !== false) {            
            $mode = Modes::getByName('hard');
        }
        
        if(strpos($leaderboard_name, 'no return') !== false) {            
            $mode = Modes::getByName('no_return');
        }
        
        if(strpos($leaderboard_name, 'phasing') !== false) {            
            $mode = Modes::getByName('phasing');
        }
        
        if(strpos($leaderboard_name, 'randomizer') !== false) {            
            $mode = Modes::getByName('randomizer');
        }
        
        if(strpos($leaderboard_name, 'mystery') !== false) {            
            $mode = Modes::getByName('mystery');
        }
        
        $mode_id = $mode['mode_id'];
        
        $character_record = Characters::getActiveByName($character_name);
        
        if(!empty($character_record)) {
            $this->character_id = $character_record['character_id'];
        }
        
        /*
            If this run is a daily then grab the date it is for.
            Date matching solution found at: http://stackoverflow.com/a/7645146
            Date filtering solution found at: http://stackoverflow.com/a/4639488  
        */
        $unformatted_daily_date = preg_replace("/[^0-9\/]/", "", $leaderboard_name);
        $daily_date = NULL;
        
        if(!empty($unformatted_daily_date)) {
            $is_daily = 1;
            $is_speedrun = 0;
            $is_score_run = 1;
            
            $daily_date = DateTime::createFromFormat('d/m/Y', $unformatted_daily_date);

            $last_errors = DateTime::getLastErrors();
            
            if(!(empty($last_errors['warning_count']) && empty($last_errors['error_count']))) {
                $daily_date = NULL;
            }
        }
        
        $daily_date_formatted = NULL;
                                                        
        if(!empty($daily_date)) {
            $daily_date_formatted = $daily_date->format('Y-m-d');  
        }                    
                                    
        //Determine if the leaderboard is a power ranking leaderboard and add it to its own group
        $score_or_speed_run = false;
        
        if(!empty($is_score_run) || !empty($is_speedrun)) {
            $score_or_speed_run = true;
        }
        
        $is_power_ranking = 0;                                        

        if(
            $score_or_speed_run && 
            empty($is_custom) && 
            empty($is_co_op) && 
            empty($is_daily) &&
            (empty($is_deathless) || !empty($is_deathless))
        ) {
            $is_power_ranking = 1;
        }
        
        $is_daily_ranking = 0;                                        
        
        if(
            !empty($is_score_run) && 
            empty($is_custom) && 
            empty($is_co_op) && 
            empty($is_seeded) && 
            !empty($is_daily) &&
            $character_name == 'cadence' &&
            !empty($daily_date)
        ) {
            $is_daily_ranking = 1;
        }        
        
        $release_id = NULL;
        
        if(!empty($is_dev)) {
            $release = Releases::getByName('early_access');
            
            $release_id = $release['release_id'];
        }
        elseif(!empty($is_prod)) {
            if(!empty($is_dlc)) {
                $release = Releases::getByName('amplified_dlc');
                
                $release_id = $release['release_id'];
            }
            else {
                $release = Releases::getByName('original');
            
                $release_id = $release['release_id'];
            }
        }
        
        $this->name = $leaderboard->name;
        $this->url = $leaderboard->url;
        $this->lbid = $leaderboard->lbid;
        $this->display_name = $leaderboard->display_name;
        $this->is_speedrun = $is_speedrun;
        $this->is_custom = $is_custom;
        $this->is_co_op = $is_co_op;
        $this->is_seeded = $is_seeded;
        $this->is_daily = $is_daily;
        $this->daily_date = $daily_date_formatted;
        $this->is_score_run = $is_score_run;
        $this->is_deathless = $is_deathless;
        $this->is_dev = $is_dev;
        $this->is_prod = $is_prod;
        $this->is_power_ranking = $is_power_ranking;
        $this->is_daily_ranking = $is_daily_ranking;    
        $this->release_id = $release_id;
        $this->mode_id = $mode_id;
    }
    
    public function isValid(DateTime $date) {
        $daily_date_difference = NULL;
        
        $daily_date = NULL;
        
        if($this->is_daily == 1 && $this->is_daily_ranking) {
            $daily_date = new DateTime($this->daily_date);
        
            $daily_date_difference = $date->diff($daily_date);
        }
        
        $blacklist_record = Blacklist::getRecordById($this->lbid);
        
        $date_within_release = false;
        
        if(!empty($this->release_id)) {
            $release = Releases::getById($this->release_id);
            
            $start_date = new DateTime($release['start_date']);
            $end_date = new DateTime($release['end_date']);
            
            if($date >= $start_date && $date <= $end_date) {
                $date_within_release = true;
            }
        }
    
        return (
            empty($blacklist_record) && 
            !empty($this->character_id) && 
            $date_within_release && 
            ($this->is_daily == 0) || ($this->is_daily == 1 && $this->is_daily_ranking == 1 && $daily_date >= $date && $daily_date_difference->format('%a') == 1)
        );
    }
}