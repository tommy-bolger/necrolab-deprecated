<?php

namespace Modules\Necrolab\Models\Leaderboards\Database\RecordModels;

use \Exception;
use \DateTime;
use \Framework\Core\RecordModel;

class Leaderboard
extends RecordModel {  
    protected $name;
    
    protected $url;
    
    protected $lbid;
    
    protected $display_name;
    
    protected $entries;
    
    protected $sortmethod;
    
    protected $displaytype;
    
    protected $onlytrustedwrites;
    
    protected $onlyfriendsreads;
    
    protected $character_id;
    
    protected $character_name;
    
    protected $is_speedrun;
    
    protected $is_custom;
    
    protected $is_co_op;
    
    protected $is_seeded;
    
    protected $is_daily;
    
    protected $daily_date;
    
    protected $daily_date_object;
    
    protected $is_score_run;
    
    protected $is_all_character;
    
    protected $is_deathless;
    
    protected $is_story_mode;

    protected $is_dev;
    
    protected $is_prod;
    
    protected $is_power_ranking;
    
    protected $is_daily_ranking; 
    
    protected function getPropertyValue($property_name, $property_value) {         
        return $property_value;
    }
    
    public function setPropertiesFromArray(array $properties) {}
    
    public function setPropertiesFromObject($leaderboard) {
        assert('is_object($leaderboard)');
    
        $leaderboard_name = strtolower($leaderboard->name);
                
        $character_id = NULL;
        $character_name = '';
        
        /*
            Retrieve which character this leaderboard is for.
            This is done by looking for a case insensitive version of each charcter's name.
            This is sloppy, but the only way I could find to fairly reliably get the character of each leaderboard with the data given.
        */
        if(strpos($leaderboard_name, 'bard') !== false) {
            $character_id = $this->characters['bard'];
            $character_name = 'bard';
        }
        elseif(strpos($leaderboard_name, 'aria') !== false) {
            $character_id = $this->characters['aria'];
            $character_name = 'aria';
        }
        elseif(strpos($leaderboard_name, 'monk') !== false) {
            $character_id = $this->characters['monk'];
            $character_name = 'monk';
        }
        elseif(strpos($leaderboard_name, 'bolt') !== false) {
            $character_id = $this->characters['bolt'];
            $character_name = 'bolt';
        }
        elseif(strpos($leaderboard_name, 'dove') !== false) {
            $character_id = $this->characters['dove'];
            $character_name = 'dove';
        }
        elseif(strpos($leaderboard_name, 'eli') !== false) {
            $character_id = $this->characters['eli'];
            $character_name = 'eli';
        }
        elseif(strpos($leaderboard_name, 'melody') !== false) {
            $character_id = $this->characters['melody'];
            $character_name = 'melody';
        }
        elseif(strpos($leaderboard_name, 'dorian') !== false) {
            $character_id = $this->characters['dorian'];
            $character_name = 'dorian';
        }
        elseif(strpos($leaderboard_name, 'coda') !== false) {
            $character_id = $this->characters['coda'];
            $character_name = 'coda';
        }
        elseif(strpos($leaderboard_name, 'ghost') !== false) {
            $character_id = $this->characters['ghost'];
            $character_name = 'ghost';
        }
        elseif(strpos($leaderboard_name, 'pacifist') !== false) {
            $character_id = $this->characters['pacifist'];
            $character_name = 'pacifist';
        }
        elseif(strpos($leaderboard_name, 'thief') !== false) {
            $character_id = $this->characters['thief'];
            $character_name = 'thief';
        }
        //If nobody else assume it's Cadence
        else {
            $character_id = $this->characters['cadence'];
            $character_name = 'cadence';
        }
        
        $is_speedrun = 0;
        $is_custom = 0;
        $is_co_op = 0;
        $is_seeded = 0;
        $is_daily = 0;
        $is_score_run = 0;
        $is_all_character = 0;
        $is_deathless = 0;
        $is_story_mode = 0;
        $is_dev = 0;
        $is_prod = 0;
        
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
        
        if(strpos($leaderboard_name, 'hardcore') !== false || strpos($leaderboard_name, 'core ') !== false || strpos($leaderboard_name, 'all zones') !== false) {
            $is_speedrun = 0;
            $is_score_run = 1;
        }
        
        if(strpos($leaderboard_name, 'all chars') !== false) {
            $is_all_character = 1;
            $character_id = $this->characters['all'];
            $character_name = 'all';
        }
        
        if(strpos($leaderboard_name, 'deathless') !== false) {
            $is_deathless = 1;
        }
        
        if(strpos($leaderboard_name, 'story') !== false) {
            $character_id = $this->characters['story'];
            $character_name = 'story';
            $is_story_mode = 1;
        }
        
        if(strpos($leaderboard_name, 'dev') !== false) {
            $is_dev = 1;
        }
        
        if(strpos($leaderboard_name, 'prod') !== false) {
            $is_prod = 1;
        }
        
        /*
            If this run is a daily then grab the date it is for.
            Date matching solution found at: http://stackoverflow.com/a/7645146
            Date filtering solution found at: http://stackoverflow.com/a/4639488  
        */
        $unformatted_daily_date = preg_replace("/[^0-9\/]/", "", $leaderboard_name);
        $daily_date = NULL;
        $daily_date_valid = true;
        $daily_date_difference = NULL;
        
        if(!empty($unformatted_daily_date)) {
            $is_daily = 1;
            $is_speedrun = 0;
            $is_score_run = 1;
            
            $daily_date = DateTime::createFromFormat('d/m/Y', $unformatted_daily_date);

            $last_errors = DateTime::getLastErrors();
            
            if(empty($last_errors['warning_count']) && empty($last_errors['error_count'])) {
                $daily_date_difference = $daily_date->diff($this->current_date);
            }
            else {
                $daily_date = NULL;
                $daily_date_valid = false;
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
            empty($is_seeded) && 
            empty($is_daily) &&
            empty($is_dev) && 
            !empty($is_prod) && 
            (empty($is_deathless) || (!empty($is_deathless) && empty($is_story_mode) && empty($is_all_character) && empty($is_speedrun)))
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
            empty($is_dev) && 
            !empty($is_prod) && 
            $character_name == 'cadence' &&
            $daily_date >= $this->live_date
        ) {
            $is_daily_ranking = 1;
        }            
        
        $this->name = $leaderboard->name;
        $this->url = $leaderboard->url;
        $this->lbid = $leaderboard->lbid;
        $this->display_name = $leaderboard->display_name;
        $this->entries = $leaderboard->entries;
        $this->sortmethod = $leaderboard->sortmethod;
        $this->displaytype = $leaderboard->displaytype;
        $this->onlytrustedwrites = $leaderboard->onlytrustedwrites;
        $this->onlyfriendsreads = $leaderboard->onlyfriendsreads;
        $this->character_id = $character_id;
        $this->character_name = $character_name;
        $this->is_speedrun = $is_speedrun;
        $this->is_custom = $is_custom;
        $this->is_co_op = $is_co_op;
        $this->is_seeded = $is_seeded;
        $this->is_daily = $is_daily;
        $this->daily_date = $daily_date_formatted;
        $this->daily_date_object = $daily_date;
        $this->daily_date_valid = $daily_date_valid;
        $this->is_score_run = $is_score_run;
        $this->is_all_character = $is_all_character;
        $this->is_deathless = $is_deathless;
        $this->is_story_mode = $is_story_mode;
        $this->is_dev = $is_dev;
        $this->is_prod = $is_prod;
        $this->is_power_ranking = $is_power_ranking;
        $this->is_daily_ranking = $is_daily_ranking;    
    }
}