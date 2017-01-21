<?php
namespace Modules\Necrolab\Models;

use \DateTime;
use \PDO;
use \Framework\Core\Framework;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis;
use \Modules\Necrolab\Objects\CacheEntryNames;
use \Modules\Necrolab\Models\Necrolab;
use \Modules\Necrolab\Models\Rankings;

class DailyRankingSeasonLeaderboards {
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
    
    public static function loadLeaderboardsIntoCache($cache = NULL) {    
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $daily_leaderboards = db()->prepareExecuteQuery("
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
                l.is_daily_ranking
            FROM daily_ranking_seasons drs
            JOIN daily_ranking_season_leaderboards drsl ON drsl.daily_ranking_season_id = drs.daily_ranking_season_id
            JOIN leaderboards l ON l.leaderboard_id = drsl.leaderboard_id
            JOIN characters c ON c.character_id = l.character_id
            WHERE drs.is_latest = 1
            ORDER BY drsl.daily_date ASC
        ");

        $transaction = $cache->transaction();

        while($daily_leaderboard = $daily_leaderboards->fetch(PDO::FETCH_ASSOC)) {
            $lbid = $daily_leaderboard['lbid'];
        
            $leaderboard_name = CacheEntryNames::generateSeasonLeaderboardName($lbid);
            
            $transaction->hMSet($leaderboard_name, $daily_leaderboard);
            $transaction->hSet(CacheEntryNames::DAILY_SEASON_LEADERBOARD_ENTRIES, $lbid, $leaderboard_name);
            $transaction->hSet(CacheEntryNames::DAILY_SEASON_LEADERBOARDS_BY_DATE, $daily_leaderboard['daily_date'], $leaderboard_name);
        }
        
        $transaction->commit();
    }
    
    public static function loadLeaderboardEntriesIntoCache($cache = NULL) {    
        if(empty($cache)) {
            $cache = cache('write');
        }
        
        $transaction = $cache->transaction();

        $daily_entries = db()->prepareExecuteQuery("
            SELECT 
                l.lbid,
                drsl.daily_date, 
                c.name AS character_name,     
                drsle.rank,
                le.details,
                le.score,
                le.is_win,
                su.steamid,
                su.personaname
            FROM daily_ranking_seasons drs
            JOIN daily_ranking_season_leaderboards drsl ON drsl.daily_ranking_season_id = drs.daily_ranking_season_id
            JOIN leaderboards l ON l.leaderboard_id = drsl.leaderboard_id
            JOIN daily_ranking_season_leaderboard_entries drsle ON drsle.daily_ranking_season_leaderboard_id = drsl.daily_ranking_season_leaderboard_id
            JOIN leaderboard_entries le ON le.leaderboard_entry_id = drsle.leaderboard_entry_id
            JOIN characters c ON c.character_id = l.character_id
            JOIN steam_users su ON su.steam_user_id = le.steam_user_id
            WHERE drs.is_latest = 1
            ORDER BY drsl.daily_date ASC, le.rank
        ");

        while($daily_entry = $daily_entries->fetch(PDO::FETCH_ASSOC)) {
            $lbid = $daily_entry['lbid'];
            $daily_date = $daily_entry['daily_date'];
            $character_name = $daily_entry['character_name'];
            $steamid = $daily_entry['steamid'];
            $rank = $daily_entry['rank'];
            $score = $daily_entry['score'];
            $personaname = $daily_entry['personaname'];

            $leaderboard_hash_name = CacheEntryNames::generateSeasonLeaderboardName($lbid);
            $entries_hash_name = CacheEntryNames::generateSeasonLeaderboardEntriesName($lbid);
            $entry_hash_name = CacheEntryNames::generateSeasonLeaderboardEntryName($lbid, $steamid);
            
            $transaction->hMSet($entry_hash_name, array(
                'lbid' => (int)$lbid,
                'steamid' => $steamid,
                'rank' => (int)$rank,
                'details' => $daily_entry['details'],
                'score' => (int)$score,
                'is_win' => (int)$daily_entry['is_win']
            ));
                
            $transaction->zAdd($entries_hash_name, $rank, $entry_hash_name);
            
            $transaction->hIncrBy(CacheEntryNames::DAILY_SEASON_LEADERBOARD_SCORE_TOTALS, $leaderboard_hash_name, $score);
            
            if(!empty($personaname)) {
                $transaction->hSet(CacheEntryNames::generateSeasonLeaderboardEntriesFilterName($lbid), $personaname, $entry_hash_name);
            }
        }

        $transaction->commit();
    }
}