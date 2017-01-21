<?php
namespace Modules\Necrolab\Models\DailySeasons\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\DailySeasons\DailySeasons as BaseDailySeasons;

class DailySeasons
extends BaseDailySeasons {    
    protected static function loadSeasons() {
        if(empty(static::$seasons)) {
            $seasons = db()->getAll("
                SELECT *
                FROM daily_ranking_seasons
            ");
            
            if(!empty($seasons)) {
                foreach($seasons as $season) {
                    static::$seasons[$season['season_number']] = $season;
                }
            }
        }
    }

    protected static function getEntriesResultset() {
        $resultset = new SQL('season_entries');
        
        $resultset->setBaseQuery("
            SELECT
                drse.rank,
                su.personaname,
                drse.first_place_ranks,
                drse.top_5_ranks,
                drse.top_10_ranks,
                drse.top_20_ranks,
                drse.top_50_ranks,
                drse.top_100_ranks,
                drse.total_points,
                drse.points_per_day,
                drse.total_dailies,
                drse.total_wins,
                drse.average_place,
                drs.season_number,
                su.steamid
            FROM daily_ranking_seasons drs
            JOIN daily_ranking_season_entries drse ON drse.daily_ranking_season_id = drs.daily_ranking_season_id
            JOIN steam_users su ON su.steam_user_id = drse.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSortCriteria('dre.rank', 'ASC');
    
        return $resultset;
    }
    
    protected static function getSeasonEntriesResultset($daily_ranking_season_id = NULL) {        
        $resultset = static::getEntriesResultset();
        
        if(!empty($daily_ranking_season_id)) {
            $resultset->addFilterCriteria('drs.daily_ranking_season_id = :daily_ranking_season_id', array(
                ':daily_ranking_season_id' => $daily_ranking_season_id
            ));
        }
        
        return $resultset;
    }
    
    protected static function getLatestSeasonEntriesResultset() {        
        $resultset = static::getEntriesResultset();
        
        $resultset->addFilterCriteria('drs.is_latest = 1');
        
        return $resultset;
    }
    
    protected static function getRankingsResultset($season_number) {
        $resultset = static::getEntriesResultset();
        
        $resultset->addFilterCriteria('drs.season_number = :season_number', array(
            ':season_number' => $season_number
        ));
        
        return $resultset;
    }
    
    public static function getSeasonEnrollment($season_number = NULL) {
        $resultset = new SQL('season_enrollment');
        
        $resultset->setBaseQuery("
            SELECT 
                dsre.*,
                su.steamid,
                drs.season_number
            FROM daily_ranking_season_enrollment dsre
            JOIN daily_ranking_seasons drs ON drs.daily_ranking_season_id = dsre.daily_ranking_season_id
            JOIN steam_users su ON dsre.steam_user_id = su.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        if(!empty($season_number)) {
            $resultset->addFilterCriteria('drs.season_number = :season_number', array(
                ':season_number' => $season_number
            ));
        }
    
        return $resultset;
    }
    
    public static function setSeasonAsLatest($daily_ranking_season_id) {
        db()->update('daily_ranking_seasons', array(
            'is_latest' => 0
        ));
        
        db()->update('daily_ranking_seasons', array(
            'is_latest' => 1
        ), array(
            'daily_ranking_season_id' => $daily_ranking_season_id
        ));
    }
    
    public static function setLastSeasonAsLatest() {
        $last_season = db()->getRow("
            SELECT 
                daily_ranking_season_id,
                is_latest
            FROM daily_ranking_seasons
            ORDER BY start_date DESC
            LIMIT 1
        ");
        
        if(!empty($last_season) && empty($last_season['is_latest'])) {
            $daily_ranking_season_id = $last_season['daily_ranking_season_id'];
            
            db()->update('daily_ranking_seasons', array(
                'is_latest' => 0
            ));
        
            db()->update('daily_ranking_seasons', array(
                'is_latest' => 1
            ), array(
                'daily_ranking_season_id' => $daily_ranking_season_id
            ));
        }
    }
    
    public static function reorderSeasons() {
        $daily_ranking_seasons = db()->getAll("
            SELECT
                daily_ranking_season_id,
                season_number,
                is_latest
            FROM daily_ranking_seasons
            ORDER BY start_date ASC
        ");
        
        if(!empty($daily_ranking_seasons)) {
            $season_number = 1;
            
            //Get the latest season ordered by date
            end($daily_ranking_seasons);
            
            $latest_season = current($daily_ranking_seasons);
            $latest_season_id = $latest_season['daily_ranking_season_id'];
            
            reset($daily_ranking_seasons);
            
            foreach($daily_ranking_seasons as $daily_ranking_season) {
                $daily_ranking_season_id = $daily_ranking_season['daily_ranking_season_id'];
                $is_latest = 0;
                
                if($daily_ranking_season_id == $latest_season_id) {
                    $is_latest = 1;
                }
                
                db()->update('daily_ranking_seasons', array(
                    'season_number' => $season_number,
                    'is_latest' => $is_latest
                ), array(
                    'daily_ranking_season_id' => $daily_ranking_season_id
                ));
            
                $season_number += 1;
            }
        }
    }
    
    public static function getSeasonLeaderboards($season_number = NULL) {
        $resultset = new SQL('season_leaderboards');

        $resultset->setBaseQuery("
            SELECT
                l.lbid,
                l.name,
                c.name AS character_name,
                l.is_speedrun,
                l.is_custom,
                l.is_co_op,
                l.is_seeded,
                l.is_daily,
                l.daily_date,
                l.is_score_run,
                l.is_all_character,
                l.is_deathless,
                l.is_story_mode,
                l.is_dev,
                l.is_prod,
                l.is_power_ranking,
                l.is_daily_ranking,
                drs.season_number
            FROM daily_ranking_seasons drs
            JOIN daily_ranking_season_leaderboards drsl ON drsl.daily_ranking_season_id = drs.daily_ranking_season_id
            JOIN leaderboards l ON l.leaderboard_id = drsl.leaderboard_id
            JOIN characters c ON c.character_id = l.character_id
            {{WHERE_CRITERIA}}
        ");
        
        if(!empty($season_number)) {
            $resultset->addFilterCriteria('drs.season_number = :season_number', array(
                ':season_number' => $season_number
            ));
        }
        
        $resultset->addSortCriteria('drsl.daily_date', 'ASC');
        
        return $resultset;
    }
    
    public static function getSeasonLeaderboardEntries($season_number = NULL) {
        $resultset = new SQL('season_leaderboard_entries');
        
        $resultset->setBaseQuery("
            SELECT 
                l.lbid,
                drsl.daily_date, 
                c.name AS character_name,     
                le.details,
                su.steamid,
                le.score,
                drsle.rank,
                le.ugcid,
                le.is_win,
                le.zone,
                le.level,
                su.personaname,
                drs.season_number
            FROM daily_ranking_seasons drs
            JOIN daily_ranking_season_leaderboards drsl ON drsl.daily_ranking_season_id = drs.daily_ranking_season_id
            JOIN leaderboards l ON l.leaderboard_id = drsl.leaderboard_id
            JOIN daily_ranking_season_leaderboard_entries drsle ON drsle.daily_ranking_season_leaderboard_id = drsl.daily_ranking_season_leaderboard_id
            JOIN leaderboard_entries le ON le.leaderboard_entry_id = drsle.leaderboard_entry_id
            JOIN characters c ON c.character_id = l.character_id
            JOIN steam_users su ON su.steam_user_id = le.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        if(!empty($season_number)) {
            $resultset->addFilterCriteria('drs.season_number = :season_number', array(
                ':season_number' => $season_number
            ));
        }
        
        $resultset->addSortCriteria('drsl.daily_date', 'ASC');
        $resultset->addSortCriteria('le.rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getLatestSeasonLeaderboardEntriesResultset($season_number = NULL) {
        $resultset = static::getSeasonLeaderboardEntries();
        
        $resultset->addFilterCriteria('drs.is_latest = 1');
        
        return $resultset;
    }
    
    public static function populateLeaderboard($daily_ranking_season_id, $leaderboard_id, DateTime $daily_date) {
        $daily_ranking_season_leaderboard_id = db()->getOne("
            SELECT daily_ranking_season_leaderboard_id
            FROM daily_ranking_season_leaderboards
            WHERE daily_ranking_season_id = :daily_ranking_season_id
                AND leaderboard_id = :leaderboard_id
        ", array(
            ':daily_ranking_season_id' => $daily_ranking_season_id,
            ':leaderboard_id' => $leaderboard_id
        ));
        
        if(empty($daily_ranking_season_leaderboard_id)) {
            $daily_ranking_season_leaderboard_id = db()->insert('daily_ranking_season_leaderboards', array(
                'daily_ranking_season_id' => $daily_ranking_season_id,
                'leaderboard_id' => $leaderboard_id,
                'daily_date' => $daily_date->format('Y-m-d')
            ));
        }
        
        db()->query("
            INSERT INTO daily_ranking_season_leaderboard_entries (daily_ranking_season_leaderboard_id, leaderboard_entry_id, steam_user_id, rank)
            SELECT 
                drsl.daily_ranking_season_leaderboard_id,
                le.leaderboard_entry_id,
                le.steam_user_id,
                row_number() OVER (ORDER BY le.score DESC) AS rank
            FROM daily_ranking_season_leaderboards drsl
            JOIN daily_ranking_season_enrollment drse ON drse.daily_ranking_season_id = drsl.daily_ranking_season_id
            JOIN leaderboards l ON l.leaderboard_id = drsl.leaderboard_id
            JOIN leaderboard_entries le ON le.leaderboard_snapshot_id = l.last_snapshot_id
                AND le.steam_user_id = drse.steam_user_id
            WHERE drsl.daily_ranking_season_leaderboard_id = :daily_ranking_season_leaderboard_id
            ORDER BY le.rank ASC
        ", array(
            ':daily_ranking_season_leaderboard_id' => $daily_ranking_season_leaderboard_id
        ));
    }
}