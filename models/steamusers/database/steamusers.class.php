<?php
namespace Modules\Necrolab\Models\SteamUsers\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\SteamUsers\SteamUsers as BaseSteamUsers;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUser as DatabaseSteamUser;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards;
use \Modules\Necrolab\Models\Rankings\Database\Power\Rankings as PowerRankings;
use \Modules\Necrolab\Models\DailyRankings\Database\DailyRankings;
use \Modules\Necrolab\Models\DailySeasons\Database\DailySeasons;

class SteamUsers
extends BaseSteamUsers {
    public static function load($steamid) {
        if(empty(static::$users[$steamid])) {
            static::$users[$steamid] = db()->getRow("
                SELECT *
                FROM steam_users
                WHERE steamid = :steamid
            ", array(
                ':steamid' => $steamid
            ));
        }
    }
    
    public static function loadIds() {
        if(empty(static::$user_ids)) {
            $user_ids = db()->getMappedColumn("
                SELECT
                    steamid,
                    steam_user_id
                FROM steam_users
            ");
            
            if(!empty($user_ids)) {
                static::$user_ids = $user_ids;
            }
        }
    }
    
    public static function getFullClassName() {
        return get_called_class();
    }
    
    public static function getAllResultset() {
        $resultset = new SQL('steam_users');
        
        $resultset->setBaseQuery("
            SELECT 
                {{SELECT_FIELDS}}
            FROM steam_users
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'steamid',
            ),
            array(
                'field' => 'personaname',
            ),
            array(
                'field' => 'twitch_username',
            ),
            array(
                'field' => 'twitter_username',
            ),
            array(
                'field' => 'nico_nico_url',
            ),
            array(
                'field' => 'hitbox_username',
            ),
            array(
                'field' => 'website',
            ),
            array(
                'field' => 'steam_user_id',
            ),
            array(
                'field' => 'updated',
            )
        ));
        
        return $resultset;
    }
    
    public static function getAllDisplayResultset() {
        $resultset = static::getAllResultset();
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addSortCriteria('personaname', 'ASC');
        
        return $resultset;
    }

    public static function getLatestPowerRanking($steamid) {
        $resultset = PowerRankings::getLatestRankingsResultset(false);
        
        $resultset->addFilterCriteria('su.steamid = :steamid', array(
            'steamid:' => $steamid
        ));
        
        return $resultset->getAll();
    }
    
    public static function getLatestDailyRankingsFromDatabase($steamid, $number_of_days = NULL, $transaction = NULL) {
        $resultset = DailyRankings::getEntriesBaseResultset();
        
        $resultset->addFilterCriteria('dr.is_latest = 1');
        
        $resultset->addFilterCriteria('su.steamid = :steamid', array(
            'steamid:' => $steamid
        ));
        
        return $resultset->getAll();
    }
    
    public static function getLatestDailyRankingSeasonEntry($steamid, $transaction = NULL) {
        $resultset = DailySeasons::getLatestSeasonEntriesResultset();
        
        $resultset->addFilterCriteria('su.steamid = :steamid', array(
            ':steamid' => $steamid
        ));
    
        return $resultset->getAll();
    }
    
    protected static function getLeaderboardEntriesResultset($steamid) {
        $leaderboard_entries = Leaderboards::getEntriesBaseResultset();
        
        $leaderboard_entries->addFilterCriteria('su.steamid = :steamid', array(
            ':steamid' => $steamid
        ));
        
        return $leaderboard_entries;
    }
    
    public static function getSpeedLeaderboards($steamid, $transaction = NULL) {
        $leaderboard_entries = static::getLeaderboardEntriesResultset($steamid);
        
        $leaderboard_entries->addFilterCriteria('l.is_speedrun = 1');
    
        return $leaderboard_entries->getAll();
    }
    
    public static function getUserScoreLeaderboards($steamid, $transaction = NULL) {
        $leaderboard_entries = static::getLeaderboardEntriesResultset($steamid);
        
        $leaderboard_entries->addFilterCriteria('l.is_score_run = 1');
    
        return $leaderboard_entries->getAll();
    }
    
    public static function getUserCustomLeaderboards($steamid, $transaction = NULL) {
        $leaderboard_entries = static::getLeaderboardEntriesResultset($steamid);
        
        $leaderboard_entries->addFilterCriteria('l.is_custom = 1');
    
        return $leaderboard_entries->getAll();
    }
    
    public static function getUserCoOpLeaderboards($steamid, $transaction = NULL) {
        $leaderboard_entries = static::getLeaderboardEntriesResultset($steamid);
        
        $leaderboard_entries->addFilterCriteria('l.is_co_op = 1');
    
        return $leaderboard_entries->getAll();
    }
    
    public static function getUserSeededLeaderboards($steamid, $transaction = NULL) {
        $leaderboard_entries = static::getLeaderboardEntriesResultset($steamid);
        
        $leaderboard_entries->addFilterCriteria('l.is_seeded = 1');
    
        return $leaderboard_entries->getAll();
    }
    
    public static function getUserDeathlessLeaderboards($steamid, $transaction = NULL) {
        $leaderboard_entries = static::getLeaderboardEntriesResultset($steamid);
        
        $leaderboard_entries->addFilterCriteria('l.is_deathless = 1');
    
        return $leaderboard_entries->getAll();
    }
    
    public static function getUserCharacterLeaderboards($steamid, $character_name, $transaction = NULL) {
        $leaderboard_entries = static::getLeaderboardEntriesResultset($steamid);
        
        $leaderboard_entries->addFilterCriteria('l.is_speedrun = 1');
    
        return $leaderboard_entries->getAll();
    }
    
    public static function getUserDailyByDateLeaderboards($steamid, $transaction = NULL) {
        $leaderboard_entries = static::getLeaderboardEntriesResultset($steamid);
        
        $leaderboard_entries->addFilterCriteria('l.is_daily_ranking = 1');
    
        return $leaderboard_entries->getAll();
    }
    
    public static function getUserDailySeasonByDateLeaderboards($steamid, $transaction = NULL) {
        $resultset = DailySeasons::getLatestSeasonLeaderboardEntriesResultset();
        
        $resultset->addFilterCriteria('su.steamid = :steamid', array(
            ':steamid' => $steamid
        ));
        
        return $resultset->getAll();
    }
    
    public static function getUserPowerLeaderboards($steamid, $transaction = NULL) {
        $leaderboard_entries = static::getLeaderboardEntriesResultset($steamid);
        
        $leaderboard_entries->addFilterCriteria('l.is_power_ranking = 1');
    
        return $leaderboard_entries->getAll();
    }
    
    protected static function getUngroupedLeaderboards($category_name) {
        return Leaderboards::getAllByCategory($category_name);
    }
    
    public static function getSocialMediaData(array $steamids) {            
        $steam_user_data = array();
        
        if(!empty($steamids)) {
            $resultset = static::getAllUsers();
        
            $resultset->addFilterCriteria("steamid IN ('" . implode("', '", $steamids) . "')");
            
            $steam_user_data = $resultset->getAll();
        }
        
        return $steam_user_data;
    }
    
    public static function getOutdatedIds() {
        $thirty_days_ago = new DateTime('-30 day');
    
        return db()->getMappedColumn("
            SELECT
                steam_user_id,
                steamid
            FROM steam_users
            WHERE updated < :updated
        ", array(
            ':updated' => $thirty_days_ago->format('Y-m-d H:i:s')
        ));
    }
    
    public static function save(DatabaseSteamUser $steam_user, $cache_query_name = NULL) {
        $steam_user_id = static::getId($steam_user->steamid);
        
        if(empty($steam_user_id)) {
            $updated = new DateTime('-31 day');
        
            $steam_user->updated = $updated->format('Y-m-d H:i:s');
        
            $steam_user_id = db()->insert('steam_users', $steam_user->toArray(), $cache_query_name);
            
            static::addId($steam_user->steamid, $steam_user_id);
        }
        else {
            $user_record = $steam_user->toArray();
            
            if(isset($user_record['steamid'])) {
                unset($user_record['steamid']);
            }
        
            db()->update('steam_users', $user_record, array(
                'steam_user_id' => $steam_user_id
            ), array(), $cache_query_name);
        }
        
        return $steam_user_id;
    }
}
