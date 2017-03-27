<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\ExternalSites\Database\ExternalSites as DatabaseExternalSites;
use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
use \Modules\Necrolab\Models\Modes\Database\Modes as DatabaseModes;
use \Modules\Necrolab\Models\Leaderboards\Database\Replays as DatabaseReplays;
use \Modules\Necrolab\Models\Leaderboards\Database\Details as DatabaseDetails;
use \Modules\Necrolab\Models\Leaderboards\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards as DatabaseLeaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Snapshots as DatabaseSnapshots;
use \Modules\Necrolab\Models\SteamUsers\Database\Pbs as DatabaseSteamUserPbs;
use \Modules\Necrolab\Models\Leaderboards\Database\RunResults as DatabaseRunResults;
use \Modules\Necrolab\Models\Leaderboards\Database\ReplayVersions as DatabaseReplayVersions;
use \Modules\Necrolab\Models\Leaderboards\Entries as BaseEntries;

class Entries
extends BaseEntries {
    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("        
            CREATE TABLE leaderboard_entries_{$date_formatted} (
                leaderboard_snapshot_id integer NOT NULL,
                steam_user_pb_id integer NOT NULL,
                rank integer NOT NULL,
                CONSTRAINT pk_leaderboard_entries_{$date_formatted}_leaderboard_entry_id PRIMARY KEY (leaderboard_snapshot_id, steam_user_pb_id, rank),
                CONSTRAINT fk_leaderboard_entries_{$date_formatted}_leaderboard_snapshot_id FOREIGN KEY (leaderboard_snapshot_id)
                    REFERENCES leaderboard_snapshots (leaderboard_snapshot_id) MATCH SIMPLE
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_leaderboard_entries_{$date_formatted}_steam_user_pb_id FOREIGN KEY (steam_user_pb_id) 
                    REFERENCES steam_user_pbs (steam_user_pb_id) MATCH SIMPLE
                    ON UPDATE CASCADE ON DELETE CASCADE
            )
            WITH (
                OIDS=FALSE
            );

            DROP INDEX IF EXISTS idx_leaderboard_entries_{$date_formatted}_leaderboard_snapshot_id;
            
            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_leaderboard_snapshot_id
            ON leaderboard_entries_{$date_formatted}
            USING btree (leaderboard_snapshot_id);
            
            DROP INDEX IF EXISTS idx_leaderboard_entries_{$date_formatted}_steam_user_pb_id;
            
            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_steam_user_pb_id
            ON leaderboard_entries_{$date_formatted}
            USING btree (steam_user_pb_id);
        ");
    }
    
    public static function clear($leaderboard_snapshot_id, DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->delete("leaderboard_entries_{$date_formatted}", array(
            'leaderboard_snapshot_id' => $leaderboard_snapshot_id
        ), array(), "leaderboard_entries_{$date_formatted}_delete");
    }
    
    public static function vacuum(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("VACUUM ANALYZE leaderboard_entries_{$date_formatted};");
    }
    
    public static function getSteamPbPopulateResultset(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        $resultset = new SQL("steam_pb_leaderboard_entries_{$date_formatted}");
        
        $resultset->setBaseQuery("
            DECLARE archived_leaderboard_data_{$date_formatted} CURSOR FOR
            {$resultset->getBaseQuery()}
        ");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'l.leaderboard_id',
                'alias' => 'leaderboard_id'
            ),
            array(
                'field' => 'le.leaderboard_snapshot_id',
                'alias' => 'leaderboard_snapshot_id'
            ),
            array(
                'field' => 'le.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'le.score',
                'alias' => 'score'
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            ),
            array(
                'field' => 'le.steam_replay_id',
                'alias' => 'steam_replay_id'
            ),
            array(
                'field' => 'le.leaderboard_entry_details_id',
                'alias' => 'leaderboard_entry_details_id'
            ),
            array(
                'field' => 'le.time',
                'alias' => 'time'
            ),
            array(
                'field' => 'le.is_win',
                'alias' => 'is_win'
            ),
            array(
                'field' => 'le.zone',
                'alias' => 'zone'
            ),
            array(
                'field' => 'le.level',
                'alias' => 'level'
            ),
            array(
                'field' => 'le.win_count',
                'alias' => 'win_count'
            ),
        ));
        
        $resultset->setFromTable('leaderboards l');

        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id');
        $resultset->addJoinCriteria("archived_leaderboard_entries_{$date_formatted} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id");

        $resultset->addFilterCriteria('ls.date BETWEEN :start_date AND :end_date', array(
            ':start_date' => $date->format('Y-m-01'),
            ':end_date' => $date->format('Y-m-t'),
        ));
        
        $resultset->addSortCriteria('ls.date', 'ASC');
        $resultset->addSortCriteria('l.leaderboard_id', 'ASC');
        $resultset->addSortCriteria('le.rank', 'ASC');
        
        return $resultset;
    }
    
    public static function closeSteamPbPopulateResultset(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("CLOSE archived_leaderboard_data_{$date_formatted};");
    }
    
    public static function getBaseResultset(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        $resultset = new SQL("leaderboard_entries_{$date_formatted}");
        
        $resultset->setFromTable('leaderboards l');
        
        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id');
        $resultset->addJoinCriteria("leaderboard_entries_{$date_formatted} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id");
        $resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id');
        
        $resultset->addFilterCriteria('ls.date = :date', array(
            ':date' => $date->format('Y-m-d')
        ));
        
        return $resultset;
    }

    public static function getAllBaseResultset(DateTime $date) {    
        $date_formatted = $date->format('Y_m');
        
        $resultset = static::getBaseResultset($date);
        
        $resultset->setName("leaderboards:entries:{$date->format('Y-m-d')}");

        DatabaseLeaderboards::setSelectFields($resultset);
        DatabaseSnapshots::setSelectFields($resultset);
        DatabaseCharacters::setSelectFields($resultset);
        DatabaseEntry::setSelectFields($resultset);
        DatabaseSteamUserPbs::setSelectFields($resultset);
        DatabaseReplays::setSelectFields($resultset);
        DatabaseDetails::setSelectFields($resultset);
        DatabaseRunResults::setSelectFields($resultset);
        DatabaseReplayVersions::setSelectFields($resultset);
        
        $resultset->addJoinCriteria('releases r ON r.release_id = l.release_id');
        $resultset->addJoinCriteria('characters c ON c.character_id = l.character_id');
        $resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = sup.leaderboard_entry_details_id');
        $resultset->addLeftJoinCriteria("
            steam_replays sr ON sr.steam_replay_id = sup.steam_replay_id
            AND sr.downloaded = 1
        ");
        $resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');        
        
        return $resultset;
    }
    
    public static function getPowerRankingsResultset(DateTime $date) {
        $resultset = static::getBaseResultset($date);
        
        $resultset->setName("leaderboards:power:entries:{$date->format('Y-m-d')}");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'l.is_speedrun',
                'alias' => 'is_speedrun'
            ),
            array(
                'field' => 'l.is_score_run',
                'alias' => 'is_score_run'
            ),
            array(
                'field' => 'l.is_deathless',
                'alias' => 'is_deathless'
            ),
            array(
                'field' => 'l.release_id',
                'alias' => 'release_id'
            ),
            array(
                'field' => 'l.mode_id',
                'alias' => 'mode_id'
            ),
            array(
                'field' => 'c.name',
                'alias' => 'character_name'
            ),
            array(
                'field' => 'le.steam_user_pb_id',
                'alias' => 'steam_user_pb_id'
            ),
            array(
                'field' => 'sup.score',
                'alias' => 'score'
            ),
            array(
                'field' => 'sup.time',
                'alias' => 'time'
            ),
            array(
                'field' => 'sup.win_count',
                'alias' => 'win_count'
            ),
            array(
                'field' => 'sup.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            )
        ));
        
        $resultset->addJoinCriteria('characters c ON c.character_id = l.character_id');        

        $resultset->addFilterCriteria('l.is_power_ranking = 1');
        
        return $resultset;
    }
    
    public static function getDailyRankingsResultset(DateTime $date, $release) {                
        $resultset = new SQL("leaderboards:entries:daily");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'l.release_id',
                'alias' => 'release_id'
            ),
            array(
                'field' => 'l.mode_id',
                'alias' => 'mode_id'
            ),
            array(
                'field' => 'ls.date',
                'alias' => 'date'
            ),
            array(
                'field' => 'sup.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            ),
            array(
                'field' => 'sup.is_win',
                'alias' => 'is_win'
            ),
            array(
                'field' => 'sup.score',
                'alias' => 'score'
            )
        ));
        
        $resultset->setFromTable('leaderboards l');
        
        $resultset->addJoinCriteria('releases r ON r.release_id = l.release_id');
        
        $resultset->addJoinCriteria("
            leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            AND ls.date = l.daily_date
        ");
        
        $resultset->addJoinCriteria("{{PARTITION_TABLE}} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id");
        $resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id');
        
        $resultset->addFilterCriteria('r.release_id = ?', array(
            $release['release_id']
        ));
        
        $resultset->addFilterCriteria('l.is_daily_ranking = 1');
        
        $resultset->addFilterCriteria("
            ls.date BETWEEN r.start_date AND COALESCE(r.end_date, ?)
            AND ls.date <= ?
        ", array(
            $date->format('Y-m-d'),
            $date->format('Y-m-d')
        ));
        
        $parition_table_names = static::getPartitionTableNames('leaderboard_entries', new DateTime($release['start_date']), new DateTime($release['end_date']));
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        return $resultset;
    }
    
    public static function getApiAllResultset(DateTime $date, $lbid) {
        $date_formatted = $date->format('Y_m');
        
        $resultset = static::getAllBaseResultset($date);
    
        $resultset->setName("api_leaderboard_entries_{$date_formatted}");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            ),
        ));
        
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        
        $resultset->addFilterCriteria('l.lbid = :lbid', array(
            ':lbid' => $lbid
        ));
        
        $resultset->addSortCriteria('le.rank', 'ASC');
        
        DatabaseExternalSites::addSiteUserLeftJoins($resultset);
        
        return $resultset;
    }
    
    public static function getApiAllDailyResultset($release_name, $mode_name, DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        $resultset = static::getAllBaseResultset($date);
    
        $resultset->setName("api_daily_leaderboard_entries_{$date_formatted}");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            ),
        ));
        
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = l.mode_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        
        $resultset->addFilterCriteria('l.daily_date = :daily_date', array(
            ':daily_date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('l.is_daily_ranking = 1');
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->addFilterCriteria('mo.name = :mode_name', array(
            ':mode_name' => $mode_name
        ));
        
        $resultset->addSortCriteria('le.rank', 'ASC');
        
        DatabaseExternalSites::addSiteUserLeftJoins($resultset);
        
        return $resultset;
    }
    
    public static function getApiSteamUserResultset(DateTime $date, $steamid, $release_name) {
        $date_formatted = $date->format('Y_m');
        
        $resultset = static::getAllBaseResultset($date);
    
        $resultset->setName("steam_user_entries_{$date_formatted}");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            ),
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            )
        ));
        
        DatabaseModes::setSelectFields($resultset);
        
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = l.mode_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        
        $resultset->addFilterCriteria('su.steamid = :steamid', array(
            ':steamid' => $steamid
        ));
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->addSortCriteria('l.name', 'ASC');
        
        return $resultset;
    }
    
    public static function getApiSteamUserScoreResultset(DateTime $date, $steamid, $release_name) {                       
        $resultset = static::getApiSteamUserResultset($date, $steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 0");
    
        return $resultset;
    }
    
    public static function getApiSteamUserSpeedResultset(DateTime $date, $steamid, $release_name) {                       
        $resultset = static::getApiSteamUserResultset($date, $steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
    
        return $resultset;
    }
    
    public static function getApiSteamUserDeathlessResultset(DateTime $date, $steamid, $release_name) {                       
        $resultset = static::getApiSteamUserResultset($date, $steamid, $release_name);
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
    
        return $resultset;
    }
    
    public static function getApiSteamUserDailyResultset(DateTime $start_date, DateTime $end_date, $steamid, $release_name) {    
        $resultset = new SQL("steam_user_leaderboard_daily_entries_{$start_date->format('Y-m-d')}_{$end_date->format('Y-m-d')}");
        
        
        DatabaseLeaderboards::setSelectFields($resultset);
        DatabaseCharacters::setSelectFields($resultset);
        DatabaseModes::setSelectFields($resultset);
        DatabaseReplays::setSelectFields($resultset);
        DatabaseDetails::setSelectFields($resultset);
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            ),
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            )
        ));
        
        DatabaseSteamUserPbs::setSelectFields($resultset);
        DatabaseEntry::setSelectFields($resultset);
        DatabaseRunResults::setSelectFields($resultset);
        DatabaseReplayVersions::setSelectFields($resultset);
        
        $resultset->setFromTable('leaderboards l');
        
        $resultset->addJoinCriteria('releases r ON r.release_id = l.release_id');
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = l.mode_id');
        $resultset->addJoinCriteria('characters c ON c.character_id = l.character_id');
        $resultset->addJoinCriteria("
            leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            AND ls.date = l.daily_date
        ");
        $resultset->addJoinCriteria("{{PARTITION_TABLE}} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id");
        $resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id');
        $resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = sup.leaderboard_entry_details_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        $resultset->addLeftJoinCriteria("
            steam_replays sr ON sr.steam_replay_id = sup.steam_replay_id
            AND sr.downloaded = 1
        ");
        $resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->addFilterCriteria('l.daily_date BETWEEN ? AND ?', array(
            $start_date->format('Y-m-d'),
            $end_date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('r.name = ?', array(
            $release_name
        ));
        
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");
        
        $parition_table_names = static::getPartitionTableNames('leaderboard_entries', $start_date, $end_date);
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        return $resultset;
    }
}