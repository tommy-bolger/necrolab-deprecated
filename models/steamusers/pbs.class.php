<?php
namespace Modules\Necrolab\Models\SteamUsers;

use \DateTime;
use \Modules\Necrolab\Models\Leaderboards\Replays;
use \Modules\Necrolab\Models\Leaderboards\Snapshots;

class Pbs {
    protected static $pbs = array();
    
    protected static $pb_ids = array();
    
    protected static function load($steam_user_pb_id) {}
    
    public static function loadIds() {}
    
    public static function get($steam_user_pb_id) {
        static::load($steam_user_pb_id);
        
        return static::$pbs[$steam_user_pb_id];
    }
    
    public static function getId($leaderboard_id, $steam_user_id, $score) {
        static::loadIds();
        
        $steam_user_pb_id = NULL;
        
        if(isset(static::$pb_ids[$leaderboard_id][$steam_user_id][$score])) {
            $steam_user_pb_id = static::$pb_ids[$leaderboard_id][$steam_user_id][$score];
        }
        
        return $steam_user_pb_id;
    }
    
    public static function addId($leaderboard_id, $steam_user_id, $score, $steam_user_pb_id) {
        $leaderboard_id = (int)$leaderboard_id;
        $steam_user_id = (int)$steam_user_id;
        $score = (int)$score;
        $steam_user_pb_id = (int)$steam_user_pb_id;
    
        static::$pb_ids[$leaderboard_id][$steam_user_id][$score] = $steam_user_pb_id;
    }
    
    public static function getFormattedApiRecord($data_row) {
        $processed_row = array();
        
        $ugcid = $data_row['ugcid'];
        
        $processed_row['date'] = Snapshots::getFormattedApiRecord($data_row);
        $processed_row['rank'] = $data_row['first_rank'];
        $processed_row['details'] = $data_row['details'];
        $processed_row['zone'] = $data_row['zone'];
        $processed_row['level'] = $data_row['level'];
        $processed_row['win'] = $data_row['is_win'];
        $processed_row['score'] = $data_row['score'];
        
        if(!empty($data_row['is_deathless'])) {
            $processed_row['win_count'] = $data_row['win_count'];
        }
        else {
            if(!empty($data_row['is_speedrun'])) {
                $processed_row['time'] = $data_row['time'];
            }
        }
        
        $processed_row['replay'] = Replays::getFormattedApiRecord($data_row);

        return $processed_row;
    }
}
