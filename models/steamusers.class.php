<?php
namespace Modules\Necrolab\Models;

use \Framework\Core\Framework;
use \Framework\Core\Loader;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;
use \HTMLPurifier_Config;
use \HTMLPurifier;

class SteamUsers {
    protected static $steam_users = array();
    
    protected static $rankings = array();
    
    protected static function loadUser($steam_user_id) {
        if(empty(self::$steam_users[$steam_user_id])) {
            if(!Framework::getInstance()->enable_cache) {
                self::$steam_users[$steam_user_id] = db()->getRow("
                    SELECT *
                    FROM steam_users
                    WHERE steam_user_id = ?
                ", array(
                    $steam_user_id
                ));
            }
            else {
                self::$steam_users[$steam_user_id] = cache()->hGetAll("steam_users:{$steam_user_id}");
            }
        }
    }

    public static function getUser($steam_user_id) {
        self::loadUser();
        
        $steam_user = array();
        
        if(!empty(self::$steam_users[$steam_user_id])) {
            $steam_user = self::$steam_users[$steam_user_id];
        }
        
        return $steam_user;
    }
    
    public static function getLatestPowerRanking($steam_user_id) {
        $latest_power_ranking = array();
        
        if(empty(self::$rankings['power'][$steam_user_id])) {
            if(!Framework::getInstance()->enable_cache) {
                $latest_power_ranking = db()->getRow("
                    SELECT
                        pre.rank,
                        su.personaname,
                        pre.score_rank,
                        pre.score_rank_points_total,
                        pre.speed_rank,
                        pre.speed_rank_points_total,
                        pre.deathless_score_rank,
                        pre.deathless_score_rank_points_total,
                        pre.total_points,
                        pre.steam_user_id,
                        pre.power_ranking_entry_id,
                        su.twitch_username,
                        su.twitter_username,
                        su.website
                    FROM steam_users su
                    JOIN power_ranking_entries pre ON pre.power_ranking_entry_id = su.latest_power_ranking_entry_id 
                    WHERE su.steam_user_id = ?   
                ", array(
                    $steam_user_id
                ));
            }
            else {
                self::loadUser($steam_user_id);
                
                if(!empty(self::$steam_users[$steam_user_id])) {
                    $steam_user = self::$steam_users[$steam_user_id];
                    
                    if(!empty($steam_user['latest_power_ranking_id'])) {
                        $latest_power_ranking = cache()->hGetAll("latest_power_rankings:{$steam_user['latest_power_ranking_id']}");
                    }
                }
            }
            
            self::$rankings['power'][$steam_user_id] = $latest_power_ranking;
        }
        else {
            $latest_power_ranking = self::$rankings['power'][$steam_user_id];
        }
        
        return $latest_power_ranking;
    }
    
    public static function getLatestScoreRanking($steam_user_id) {
        $latest_score_ranking = array();
        
        if(empty(self::$rankings['score'][$steam_user_id])) {
            if(!Framework::getInstance()->enable_cache) {
                $latest_score_ranking = db()->getRow("
                    SELECT
                        pre.score_rank,
                        su.personaname,
                        pre.cadence_score_rank,
                        pre.bard_score_rank,
                        pre.monk_score_rank,
                        pre.aria_score_rank,
                        pre.bolt_score_rank,
                        pre.dove_score_rank,
                        pre.eli_score_rank,
                        pre.melody_score_rank,
                        pre.dorian_score_rank,
                        pre.all_score_rank,
                        pre.story_score_rank,
                        pre.score_rank_points_total,
                        pre.steam_user_id,
                        pre.power_ranking_entry_id,
                        su.twitch_username,
                        su.twitter_username,
                        su.website
                    FROM steam_users su
                    JOIN power_ranking_entries pre ON pre.power_ranking_entry_id = su.latest_power_ranking_entry_id 
                    WHERE su.steam_user_id = ?   
                        AND pre.score_rank IS NOT NULL
                ", array(
                    $steam_user_id
                ));
            }
            else {
                self::loadUser($steam_user_id);
                
                if(!empty(self::$steam_users[$steam_user_id])) {
                    $steam_user = self::$steam_users[$steam_user_id];
                    
                    if(!empty($steam_user['latest_score_ranking_id'])) {
                        $latest_score_ranking = cache()->hGetAll("latest_score_rankings:{$steam_user['latest_score_ranking_id']}");
                    }
                }
            }
            
            self::$rankings['score'][$steam_user_id] = $latest_score_ranking;
        }
        else {
            $latest_score_ranking = self::$rankings['score'][$steam_user_id];
        }
        
        return $latest_score_ranking;
    }
    
    public static function getLatestSpeedRanking($steam_user_id) {
        $latest_speed_ranking = array();
        
        if(empty(self::$rankings['speed'][$steam_user_id])) {
            if(!Framework::getInstance()->enable_cache) {
                $latest_speed_ranking = db()->getRow("
                    SELECT
                        pre.speed_rank,
                        su.personaname,
                        pre.cadence_speed_rank,
                        pre.bard_speed_rank,
                        pre.monk_speed_rank,
                        pre.aria_speed_rank,
                        pre.bolt_speed_rank,
                        pre.dove_speed_rank,
                        pre.eli_speed_rank,
                        pre.melody_speed_rank,
                        pre.dorian_speed_rank,
                        pre.all_speed_rank,
                        pre.story_speed_rank,
                        pre.speed_rank_points_total,
                        pre.steam_user_id,
                        pre.power_ranking_entry_id,
                        su.twitch_username,
                        su.twitter_username,
                        su.website
                    FROM steam_users su
                    JOIN power_ranking_entries pre ON pre.power_ranking_entry_id = su.latest_power_ranking_entry_id 
                    WHERE su.steam_user_id = ?   
                        AND pre.speed_rank IS NOT NULL
                ", array(
                    $steam_user_id
                ));
            }
            else {
                self::loadUser($steam_user_id);
                
                if(!empty(self::$steam_users[$steam_user_id])) {
                    $steam_user = self::$steam_users[$steam_user_id];
                    
                    if(!empty($steam_user['latest_speed_ranking_id'])) {
                        $latest_speed_ranking = cache()->hGetAll("latest_speed_rankings:{$steam_user['latest_speed_ranking_id']}");
                    }
                }
            }
            
            self::$rankings['speed'][$steam_user_id] = $latest_speed_ranking;
        }
        else {
            $latest_speed_ranking = self::$rankings['speed'][$steam_user_id];
        }
        
        return $latest_speed_ranking;
    }
    
    public static function getLatestDeathlessScoreRanking($steam_user_id) {
        $latest_deathless_score_ranking = array();
        
        if(empty(self::$rankings['deathless_score'][$steam_user_id])) {
            if(!Framework::getInstance()->enable_cache) {
                $latest_deathless_score_ranking = db()->getRow("
                    SELECT
                        pre.deathless_score_rank,
                        su.personaname,
                        pre.cadence_deathless_score_rank,
                        pre.bard_deathless_score_rank,
                        pre.monk_deathless_score_rank,
                        pre.aria_deathless_score_rank,
                        pre.bolt_deathless_score_rank,
                        pre.dove_deathless_score_rank,
                        pre.eli_deathless_score_rank,
                        pre.melody_deathless_score_rank,
                        pre.dorian_deathless_score_rank,
                        pre.all_deathless_score_rank,
                        pre.story_deathless_score_rank,
                        pre.deathless_score_rank_points_total,
                        pre.steam_user_id,
                        pre.power_ranking_entry_id,
                        su.twitch_username,
                        su.twitter_username,
                        su.website
                    FROM steam_users su
                    JOIN power_ranking_entries pre ON pre.power_ranking_entry_id = su.latest_power_ranking_entry_id 
                    WHERE su.steam_user_id = ?   
                        AND pre.deathless_score_rank IS NOT NULL
                ", array(
                    $steam_user_id
                ));
            }
            else {
                self::loadUser($steam_user_id);
                
                if(!empty(self::$steam_users[$steam_user_id])) {
                    $steam_user = self::$steam_users[$steam_user_id];
                    
                    if(!empty($steam_user['latest_deathless_score_id'])) {
                        $latest_deathless_score_ranking = cache()->hGetAll("latest_deathless_score_rankings:{$steam_user['latest_deathless_score_id']}");
                    }
                }
            }
            
            self::$rankings['deathless_score'][$steam_user_id] = $latest_deathless_score_ranking;
        }
        else {
            $latest_deathless_score_ranking = self::$rankings['deathless_score'][$steam_user_id];
        }
        
        return $latest_deathless_score_ranking;
    }
    
    public static function getUserPowerRankingsFromBeginning() {
        
    }
    
    public static function saveSocialMediaData($steam_user_id, $twitch_username, $twitter_username, $website) {
        Loader::load('HTMLPurifier/HTMLPurifier.auto.php');
        
        $purifier_configuration = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($purifier_configuration);
        
        $twitch_username = $purifier->purify($twitch_username);
        $twitter_username = $purifier->purify($twitter_username);
        $website = $purifier->purify($website);
    
        $social_media_data = array(
            'twitch_username' => $twitch_username,
            'twitter_username' => $twitter_username,
            'website' => $website
        );
    
        db()->update('steam_users', $social_media_data, array(
            'steam_user_id' => $steam_user_id
        ));
        
        if(Framework::getInstance()->enable_cache) {
            self::loadUser($steam_user_id);
            
            $steam_user = array();
            
            if(!empty(self::$steam_users[$steam_user_id])) {
                $steam_user = self::$steam_users[$steam_user_id];
            }
            
            if(!empty($steam_user['latest_power_ranking_id'])) {   
                cache()->hMSet("latest_power_rankings:{$steam_user['latest_power_ranking_id']}", $social_media_data);
            }
            
            if(!empty($steam_user['latest_score_ranking_id'])) {  
                cache()->hMSet("latest_score_rankings:{$steam_user['latest_score_ranking_id']}", $social_media_data);
            }
            
            if(!empty($steam_user['latest_speed_ranking_id'])) { 
                cache()->hMSet("latest_speed_rankings:{$steam_user['latest_speed_ranking_id']}", $social_media_data);
            }
            
            if(!empty($steam_user['latest_deathless_score_id'])) {  
                cache()->hMSet("latest_deathless_score_rankings:{$steam_user['latest_deathless_score_id']}", $social_media_data);
            }
        }
    }
}