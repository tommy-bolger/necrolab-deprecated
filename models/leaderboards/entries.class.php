<?php
namespace Modules\Necrolab\Models\Leaderboards;

use \DateTime;
use \Modules\Necrolab\Models\Necrolab;
use \Framework\Modules\Module;

class Entries
extends Necrolab {
    protected $lbid;
    
    protected $entries = array();
    
    public static function saveXml($lbid, DateTime $date, $page, $xml) {
        $installation_path = Module::getInstance('necrolab')->getInstallationPath();
        $snapshot_path = "{$installation_path}/leaderboard_xml/{$date->format('Y-m-d')}/{$lbid}";
        
        if(!is_dir($snapshot_path)) {
            mkdir($snapshot_path);
        }
    
        file_put_contents("{$snapshot_path}/page_{$page}.xml.gz", gzencode($xml, 9));
    }

    protected static function getRankingsResultset($lbid, $leaderboard_type) {}
    
    public static function getSteamUsersFromResultData(array $result_data) {}
    
    public static function processRankingsResultSet($leaderboard_type, array $result_data) {
        $processed_data = array();
        
        if(!empty($result_data)) {        
            $cache = cache('read');
        
            $grouped_steam_user_data = static::getSteamUsersFromResultData($result_data);
            
            foreach($result_data as $result_row) {
                $steamid = $result_row['steamid'];

                $personaname = NULL;
                $twitch_username = NULL;
                $nico_nico_url = NULL;
                $hitbox_username = NULL;
                $twitter_username = NULL;
                $website = NULL;
                
                if(!empty($grouped_steam_user_data[$steamid])) {
                    $steam_user_record = $grouped_steam_user_data[$steamid];
                    
                    if(!empty($steam_user_record['personaname'])) {
                        $personaname = $steam_user_record['personaname'];
                    }
                    
                    if(!empty($steam_user_record['twitch_username'])) {
                        $twitch_username = $steam_user_record['twitch_username'];
                    }
                    
                    if(!empty($steam_user_record['nico_nico_url'])) {
                        $nico_nico_url = $steam_user_record['nico_nico_url'];
                    }
                    
                    if(!empty($steam_user_record['hitbox_username'])) {
                        $hitbox_username = $steam_user_record['hitbox_username'];
                    }
                    
                    if(!empty($steam_user_record['twitter_username'])) {
                        $twitter_username = $steam_user_record['twitter_username'];
                    }
                    
                    if(!empty($steam_user_record['website'])) {
                        $website = $steam_user_record['website'];
                    }
                }
                
                $processed_row = array(
                    'steamid' => $steamid,
                    'rank' => $result_row['rank'],
                    'social_media' => array(
                        'twitch_username' => $twitch_username,
                        'nico_nico_url' => $nico_nico_url,
                        'hitbox_username' => $hitbox_username,
                        'twitter_username' => $twitter_username,
                        'website' => $website
                    ),
                    'personaname' => $personaname
                );
                
                switch($leaderboard_type) {
                    case 'score':
                        $processed_row['score'] = $result_row['score'];
                        $processed_row['highest_zone'] = $result_row['zone'];
                        $processed_row['highest_level'] = $result_row['level'];
                        $processed_row['is_win'] = $result_row['is_win'];
                        break;
                    case 'speed':
                        $processed_row['time'] = $result_row['time'];
                        break;
                    case 'deathless':
                        $processed_row['wins'] = $result_row['win_count'];
                        $processed_row['highest_zone'] = $result_row['zone'];
                        $processed_row['highest_level'] = $result_row['level'];
                        $processed_row['is_win'] = $result_row['is_win'];
                        break;
                }
                
                $processed_data[] = $processed_row;
            }
        }
        
        return $processed_data;
    }
    
    public function __construct($lbid) {
        parent::__construct();
    
        $this->lbid = $lbid;
    }

    public function add($leaderboard_entry) {
        $this->entries[] = $leaderboard_entry->toArray(false);
    }
    
    public function save(DateTime $date) {}
}