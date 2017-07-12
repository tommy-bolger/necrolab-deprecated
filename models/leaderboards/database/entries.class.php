<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\Database\InsertQueue;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis\Hybrid as HybridResultset;
use \Modules\Necrolab\Models\ExternalSites;
use \Modules\Necrolab\Models\Characters;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\Leaderboards\Database\Replays as DatabaseReplays;
use \Modules\Necrolab\Models\Leaderboards\Database\Details as DatabaseDetails;
use \Modules\Necrolab\Models\Leaderboards\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards as DatabaseLeaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Snapshots as DatabaseSnapshots;
use \Modules\Necrolab\Models\SteamUsers\Database\Pbs as DatabaseSteamUserPbs;
use \Modules\Necrolab\Models\Leaderboards\Database\RunResults as DatabaseRunResults;
use \Modules\Necrolab\Models\Leaderboards\Database\ReplayVersions as DatabaseReplayVersions;
use \Modules\Necrolab\Models\Leaderboards\CacheNames;
use \Modules\Necrolab\Models\Leaderboards\Entries as BaseEntries;

class Entries
extends BaseEntries {
    public static function dropPartitionTableConstraints(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("
            ALTER TABLE leaderboard_entries_{$date_formatted}
            DROP CONSTRAINT fk_leaderboard_entries_{$date_formatted}_leaderboard_snapshot_id,
            DROP CONSTRAINT fk_leaderboard_entries_{$date_formatted}_steam_user_pb_id;
        ");
    }
    
    public static function createPartitionTableConstraints(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        db()->exec("
            ALTER TABLE leaderboard_entries_{$date_formatted}
            ADD CONSTRAINT fk_leaderboard_entries_{$date_formatted}_leaderboard_snapshot_id FOREIGN KEY (leaderboard_snapshot_id)
                REFERENCES leaderboard_snapshots (leaderboard_snapshot_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT fk_leaderboard_entries_{$date_formatted}_steam_user_pb_id FOREIGN KEY (steam_user_pb_id) 
                REFERENCES steam_user_pbs (steam_user_pb_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE;
        ");
    }

    public static function dropPartitionTableIndexes(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        db()->exec("
            DROP INDEX IF EXISTS idx_leaderboard_entries_{$date_formatted}_steam_user_pb_id;
        ");
        
        /*db()->exec("
            DROP INDEX IF EXISTS 
                idx_leaderboard_entries_{$date_formatted}_steam_user_pb_id,
                idx_leaderboard_entries_{$date_formatted}_rank;
        ");*/
    }
    
    public static function createPartitionTableIndexes(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        db()->exec("
            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_steam_user_pb_id
            ON leaderboard_entries_{$date_formatted}
            USING btree (steam_user_pb_id);
        ");
    
        /*db()->exec("
            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_steam_user_pb_id
            ON leaderboard_entries_{$date_formatted}
            USING btree (steam_user_pb_id);
            
            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_rank
            ON leaderboard_entries_{$date_formatted}
            USING btree (rank);
        ");*/
    }

    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        static::dropPartitionTableIndexes($date);
    
        db()->exec("        
            CREATE TABLE leaderboard_entries_{$date_formatted} (
                leaderboard_snapshot_id integer NOT NULL,
                steam_user_pb_id integer NOT NULL,
                rank integer NOT NULL,
                CONSTRAINT pk_leaderboard_entries_{$date_formatted}_leaderboard_entry_id PRIMARY KEY (leaderboard_snapshot_id, steam_user_pb_id, rank)
            )
            WITH (
                OIDS=FALSE
            );
        ");
        
        static::createPartitionTableConstraints($date);
        static::createPartitionTableIndexes($date);
    }
    
    public static function clear($leaderboard_snapshot_id, DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->delete("leaderboard_entries_{$date_formatted}", array(
            'leaderboard_snapshot_id' => $leaderboard_snapshot_id
        ), array(), "leaderboard_entries_{$date_formatted}_delete");
    }
    
    public static function getInsertQueue(DateTime $date) {
        return new InsertQueue("leaderboard_entries_{$date->format('Y_m')}", db(), 10000);
    }
    
    public static function vacuum(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("VACUUM ANALYZE leaderboard_entries_{$date_formatted};");
    }
    
    public static function createTemporaryTable() {
        db()->exec("
            CREATE TEMPORARY TABLE leaderboard_entries (
                leaderboard_snapshot_id integer NOT NULL,
                steam_user_pb_id integer NOT NULL,
                rank integer NOT NULL
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function getTempInsertQueue() {
        return new InsertQueue("leaderboard_entries", db(), 20000);
    }
    
    public static function saveTempEntries(DateTime $date) {
        db()->exec("
            INSERT INTO leaderboard_entries_{$date->format('Y_m')}
            SELECT *
            FROM leaderboard_entries
        ");
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
    
    public static function getPowerRankingsResultset(DateTime $date) {
        $resultset = static::getBaseResultset($date);
        
        $resultset->setName("leaderboards_power_entries_{$date->format('Y_m_d')}");
        
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
                'field' => 'l.is_seeded',
                'alias' => 'is_seeded'
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
        $resultset = new SQL("leaderboards_entries_daily_{$date->format('Y_m_d')}");
        
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
    
    public static function getApiAllResultset(DateTime $date, $leaderboard_id, $external_site_id) {        
        $date_formatted = $date->format('Y_m');
        
        //The base data resultset
        $sql_resultset = new SQL("leaderboard_entries_{$date_formatted}");
        
        $sql_resultset->addSelectFields(array(
            array(
                'field' => 'l.is_speedrun',
                'alias' => 'is_speedrun'
            ),
            array(
                'field' => 'l.is_deathless',
                'alias' => 'is_deathless'
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            ),
            array(
                'field' => 'led.details',
                'alias' => 'details'
            ),
            array(
                'field' => 'sup.zone',
                'alias' => 'zone'
            ),
            array(
                'field' => 'sup.level',
                'alias' => 'level'
            ),
            array(
                'field' => 'sup.is_win',
                'alias' => 'is_win'
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
                'field' => 'sr.ugcid',
                'alias' => 'ugcid'
            ),
            array(
                'field' => 'sr.seed',
                'alias' => 'seed'
            ),
            array(
                'field' => 'sr.downloaded',
                'alias' => 'downloaded'
            ),
            array(
                'field' => 'srv.name',
                'alias' => 'version'
            ),
            array(
                'field' => 'rr.name',
                'alias' => 'run_result'
            )
        ));
        
        $sql_resultset->setFromTable('leaderboards l');
        
        $sql_resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id');
        $sql_resultset->addJoinCriteria("leaderboard_entries_{$date_formatted} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id");
        $sql_resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id');
        $sql_resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = sup.leaderboard_entry_details_id');
        $sql_resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        
        ExternalSites::addSiteUserLeftJoins($sql_resultset);
        
        $sql_resultset->addLeftJoinCriteria("
            steam_replays sr ON sr.steam_replay_id = sup.steam_replay_id
                AND sr.downloaded = 1
                AND sr.invalid = 0
        ");
        $sql_resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $sql_resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');      
        
        $sql_resultset->addFilterCriteria('l.leaderboard_id = ?', array(
            $leaderboard_id
        ));
        
        $sql_resultset->addFilterCriteria('ls.date = ?', array(
            $date->format('Y-m-d')
        ));
        
        $sql_resultset->setSortCriteria('le.rank', 'ASC');
        
        //The full resultset
        $resultset = new HybridResultset("leaderboard_entries_{$date_formatted}", cache('database'), cache('local'));
        
        $resultset->setSqlResultset($sql_resultset, 'su.steam_user_id');
        
        $resultset->setPartitionName($date->format('Y-m-d'));
        
        $resultset->setIndexName(CacheNames::getIndexName($leaderboard_id, array(
            $external_site_id
        )));        
        
        return $resultset;
    }
    
    public static function getApiAllDailyResultset($release_id, $mode_id, $external_site_id, DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        //The base data resultset
        $sql_resultset = new SQL("daily_leaderboard_entries_{$date_formatted}");
        
        $sql_resultset->addSelectFields(array(
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            ),
            array(
                'field' => 'led.details',
                'alias' => 'details'
            ),
            array(
                'field' => 'sup.zone',
                'alias' => 'zone'
            ),
            array(
                'field' => 'sup.level',
                'alias' => 'level'
            ),
            array(
                'field' => 'sup.is_win',
                'alias' => 'is_win'
            ),
            array(
                'field' => 'sup.score',
                'alias' => 'score'
            ),
            array(
                'field' => 'sr.ugcid',
                'alias' => 'ugcid'
            ),
            array(
                'field' => 'sr.seed',
                'alias' => 'seed'
            ),
            array(
                'field' => 'sr.downloaded',
                'alias' => 'downloaded'
            ),
            array(
                'field' => 'srv.name',
                'alias' => 'version'
            ),
            array(
                'field' => 'rr.name',
                'alias' => 'run_result'
            )
        ));
        
        $sql_resultset->setFromTable('leaderboards l');
        
        $sql_resultset->addJoinCriteria("
            leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
                AND ls.date = l.daily_date
        ");
        $sql_resultset->addJoinCriteria("leaderboard_entries_{$date_formatted} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id");
        $sql_resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id');
        $sql_resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = sup.leaderboard_entry_details_id');
        $sql_resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        
        ExternalSites::addSiteUserLeftJoins($sql_resultset);
        
        $sql_resultset->addLeftJoinCriteria("
            steam_replays sr ON sr.steam_replay_id = sup.steam_replay_id
                AND sr.downloaded = 1
                AND sr.invalid = 0
        ");
        $sql_resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $sql_resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');      
        
        $sql_resultset->addFilterCriteria('l.release_id = ?', array(
            $release_id
        ));
        
        $sql_resultset->addFilterCriteria('l.mode_id = ?', array(
            $mode_id
        ));
        
        $sql_resultset->addFilterCriteria('l.is_daily_ranking = 1');
        
        $sql_resultset->addFilterCriteria('l.daily_date = ?', array(
            $date->format('Y-m-d')
        ));
        
        $sql_resultset->setSortCriteria('le.rank', 'ASC');
        
        //The full resultset
        $resultset = new HybridResultset("daily_leaderboard_entries_{$date_formatted}", cache('database'), cache('local'));
        
        $resultset->setSqlResultset($sql_resultset, 'su.steam_user_id');
        
        $resultset->setPartitionName($date->format('Y-m-d'));
        
        $resultset->setIndexName(CacheNames::getDailyIndexName(array(
            $release_id,
            $mode_id,
            $external_site_id
        )));        
        
        return $resultset;
    }
    
    public static function getApiSteamUserResultset(DateTime $date, $steamid, $release_id, $mode_id, $seeded, $co_op, $custom) {        
        $date_formatted = $date->format('Y_m');
        
        //The base data resultset
        $resultset = new SQL("leaderboard_entries_{$date_formatted}");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'l.is_speedrun',
                'alias' => 'is_speedrun'
            ),
            array(
                'field' => 'l.is_deathless',
                'alias' => 'is_deathless'
            ),
            array(
                'field' => 'c.name',
                'alias' => 'character_name'
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            ),
            array(
                'field' => 'led.details',
                'alias' => 'details'
            ),
            array(
                'field' => 'sup.zone',
                'alias' => 'zone'
            ),
            array(
                'field' => 'sup.level',
                'alias' => 'level'
            ),
            array(
                'field' => 'sup.is_win',
                'alias' => 'is_win'
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
                'field' => 'sr.ugcid',
                'alias' => 'ugcid'
            ),
            array(
                'field' => 'sr.seed',
                'alias' => 'seed'
            ),
            array(
                'field' => 'sr.downloaded',
                'alias' => 'downloaded'
            ),
            array(
                'field' => 'srv.name',
                'alias' => 'version'
            ),
            array(
                'field' => 'rr.name',
                'alias' => 'run_result'
            )
        ));
        
        DatabaseLeaderboards::setSelectFields($resultset);
        
        $resultset->setFromTable('leaderboards l');
        
        $resultset->addJoinCriteria('characters c ON c.character_id = l.character_id');
        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id');
        $resultset->addJoinCriteria("leaderboard_entries_{$date_formatted} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id");
        $resultset->addJoinCriteria('steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id');
        $resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = sup.leaderboard_entry_details_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = sup.steam_user_id');
        
        $resultset->addLeftJoinCriteria("
            steam_replays sr ON sr.steam_replay_id = sup.steam_replay_id
                AND sr.downloaded = 1
                AND sr.invalid = 0
        ");
        $resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');      
        
        $resultset->addFilterCriteria('l.release_id = :release_id', array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria('l.mode_id = :mode_id', array(
            ':mode_id' => $mode_id
        ));
        
        $resultset->addFilterCriteria('l.is_seeded = :seeded', array(
            ':seeded' => $seeded
        ));
        
        $resultset->addFilterCriteria('l.is_co_op = :co_op', array(
            ':co_op' => $co_op
        ));
        
        $resultset->addFilterCriteria('l.is_custom = :custom', array(
            ':custom' => $custom
        ));
        
        $resultset->addFilterCriteria("l.is_daily = 0");
        
        $resultset->addFilterCriteria('ls.date = :date', array(
            ':date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('su.steamid = :steamid', array(
            ':steamid' => $steamid
        ));
        
        $resultset->addSortCriteria('c.sort_order', 'ASC');
        $resultset->addSortCriteria('l.name', 'ASC');
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
        
        return $resultset;
    }
    
    public static function getApiSteamUserScoreResultset(DateTime $date, $steamid, $release_id, $mode_id, $seeded, $co_op, $custom) {                       
        $resultset = static::getApiSteamUserResultset($date, $steamid, $release_id, $mode_id, $seeded, $co_op, $custom);
        
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_deathless = 0");
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
    
        return $resultset;
    }
    
    public static function getApiSteamUserSpeedResultset(DateTime $date, $steamid, $release_id, $mode_id, $seeded, $co_op, $custom) {                       
        $resultset = static::getApiSteamUserResultset($date, $steamid, $release_id, $mode_id, $seeded, $co_op, $custom);
        
        $resultset->addFilterCriteria("l.is_speedrun = 1");
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
    
        return $resultset;
    }
    
    public static function getApiSteamUserDeathlessResultset(DateTime $date, $steamid, $release_id, $mode_id, $seeded, $co_op, $custom) {                       
        $resultset = static::getApiSteamUserResultset($date, $steamid, $release_id, $mode_id, $seeded, $co_op, $custom);
        
        $resultset->addFilterCriteria("l.is_deathless = 1");
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
    
        return $resultset;
    }
    
    public static function getApiSteamUserDailyResultset(DateTime $start_date, DateTime $end_date, $steamid, $release_id, $mode_id) {    
        $resultset = new SQL("steam_user_leaderboard_daily_entries_{$start_date->format('Y-m-d')}_{$end_date->format('Y-m-d')}");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'l.daily_date',
                'alias' => 'daily_date'
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            ),
            array(
                'field' => 'led.details',
                'alias' => 'details'
            ),
            array(
                'field' => 'sup.zone',
                'alias' => 'zone'
            ),
            array(
                'field' => 'sup.level',
                'alias' => 'level'
            ),
            array(
                'field' => 'sup.is_win',
                'alias' => 'is_win'
            ),
            array(
                'field' => 'sup.score',
                'alias' => 'score'
            ),
            array(
                'field' => 'sr.ugcid',
                'alias' => 'ugcid'
            ),
            array(
                'field' => 'sr.seed',
                'alias' => 'seed'
            ),
            array(
                'field' => 'sr.downloaded',
                'alias' => 'downloaded'
            ),
            array(
                'field' => 'srv.name',
                'alias' => 'version'
            ),
            array(
                'field' => 'rr.name',
                'alias' => 'run_result'
            )
        ));
        
        $resultset->setFromTable('leaderboards l');
        
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
                AND sr.invalid = 0
        ");
        $resultset->addLeftJoinCriteria('run_results rr ON rr.run_result_id = sr.run_result_id');
        $resultset->addLeftJoinCriteria('steam_replay_versions srv ON srv.steam_replay_version_id = sr.steam_replay_version_id');      
        
        $resultset->addFilterCriteria('l.release_id = ?', array(
            $release_id
        ));
        
        $resultset->addFilterCriteria('l.mode_id = ?', array(
            $mode_id
        ));
        
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");
        
        $resultset->addFilterCriteria('l.daily_date BETWEEN ? AND ?', array(
            $start_date->format('Y-m-d'),
            $end_date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->setSortCriteria('l.daily_date', 'DESC');
        
        $parition_table_names = static::getPartitionTableNames('leaderboard_entries', $start_date, $end_date);
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);
        
        return $resultset;
    }

    public static function loadIntoCache(DateTime $date) { 
        $date_formatted = $date->format('Y-m-d');
    
        $resultset = new SQL("leaderboard_entries");
            
        $resultset->setBaseQuery("
            {{SELECT_FIELDS}}
            FROM leaderboards l
            LEFT JOIN leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id
            JOIN leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            JOIN leaderboard_entries_{$date->format('Y_m')} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            JOIN steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id
            JOIN steam_users su ON su.steam_user_id = sup.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'ls.leaderboard_id',
                'alias' => 'leaderboard_id'
            ),
            array(
                'field' => 'su.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            )
        ));
        
        ExternalSites::addSiteIdSelectFields($resultset);
        
        $resultset->addFilterCriteria("l.is_daily = 0");
        
        $resultset->addFilterCriteria("lb.leaderboards_blacklist_id IS NULL");
        
        $resultset->addFilterCriteria("ls.date = :date", array(
            ':date' => $date_formatted
        ));

        $resultset->setAsCursor(100000);
        
        ExternalSites::loadAll();
        
        db()->beginTransaction();
        
        $transaction = cache('database')->transaction();
        
        $resultset->prepareExecuteQuery();
        
        $entries = array();
        $indexes = array();
        
        do {
            $entries = $resultset->getNextCursorChunk();
        
            if(!empty($entries)) {
                foreach($entries as $entry) {
                    $leaderboard_id = (int)$entry['leaderboard_id'];
                    $steam_user_id = (int)$entry['steam_user_id'];
                    $rank = (int)$entry['rank'];
                    
                    $users_index_base_name = CacheNames::getIndexName($leaderboard_id, array());
                        
                    ExternalSites::addToSiteIdIndexes($indexes, $entry, $users_index_base_name, $steam_user_id, $rank);
                }
            }
        }
        while(!empty($entries));
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                ksort($index_data);
            
                $transaction->set($date_formatted, static::encodeRecord($index_data), $key);
            }
        }
        
        $transaction->commit();
        
        db()->commit();
    }
    
    public static function loadDailiesIntoCache(DateTime $date) { 
        $date_formatted = $date->format('Y-m-d');
    
        $resultset = new SQL("leaderboard_entries");
            
        $resultset->setBaseQuery("
            {{SELECT_FIELDS}}
            FROM leaderboards l
            LEFT JOIN leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id
            JOIN leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            JOIN leaderboard_entries_{$date->format('Y_m')} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            JOIN steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id
            JOIN steam_users su ON su.steam_user_id = sup.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'l.leaderboard_id',
                'alias' => 'leaderboard_id'
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
                'field' => 'l.daily_date',
                'alias' => 'daily_date'
            ),
            array(
                'field' => 'su.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'le.rank',
                'alias' => 'rank'
            )
        ));
        
        ExternalSites::addSiteIdSelectFields($resultset);
        
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");
        
        $resultset->addFilterCriteria("lb.leaderboards_blacklist_id IS NULL");
        
        $resultset->addFilterCriteria("ls.date = :date", array(
            ':date' => $date_formatted
        ));

        $resultset->setAsCursor(100000);
        
        ExternalSites::loadAll();
        
        db()->beginTransaction();
        
        $transaction = cache('database')->transaction();
        
        $resultset->prepareExecuteQuery();
        
        $entries = array();
        $daily_indexes = array();
        
        do {
            $entries = $resultset->getNextCursorChunk();
        
            if(!empty($entries)) {
                foreach($entries as $entry) {
                    $daily_date = $entry['daily_date'];
                    $daily_date_object = new DateTime($daily_date);
                    
                    if($daily_date_object <= $date) {
                        $leaderboard_id = (int)$entry['leaderboard_id'];
                        $steam_user_id = (int)$entry['steam_user_id'];
                        $release_id = (int)$entry['release_id'];
                        $mode_id = (int)$entry['mode_id'];
                        $rank = (int)$entry['rank'];
                    
                        if(empty($daily_indexes[$daily_date])) {
                            $daily_indexes[$daily_date] = array();
                        }
                        
                        $users_index_base_name = CacheNames::getDailyIndexName(array(
                            $release_id,
                            $mode_id
                        ));
                        
                        ExternalSites::addToSiteIdIndexes($daily_indexes[$daily_date], $entry, $users_index_base_name, $steam_user_id, $rank);
                    }
                }
            }
        }
        while(!empty($entries));
        
        if(!empty($daily_indexes)) {
            foreach($daily_indexes as $daily_date => $daily_index_data) {
                foreach($daily_index_data as $key => $index_data) {
                    ksort($index_data);
                
                    $transaction->set($daily_date, static::encodeRecord($index_data), $key);
                }
            }
        }
        
        $transaction->commit();
        
        db()->commit();
    }
    
    /*protected static function saveIntoCache($date_formatted, $leaderboard_id, $transaction, &$entries, &$indexes) {
        if(!empty($entries)) {
            $transaction->hSet(CacheNames::getEntriesName($leaderboard_id), $date_formatted, static::encodeRecord($entries));
                                
            if(!empty($indexes)) {
                foreach($indexes as $key => $index_data) {
                    $transaction->hSet($key, $date_formatted, static::encodeRecord($index_data));
                }
            }
        }
    }
    
    public static function loadIntoCache(DateTime $date) { 
        $date_formatted = $date->format('Y-m-d');
    
        $resultset = new SQL("leaderboard_entries");
            
        $resultset->setBaseQuery("
            SELECT 
                l.release_id,
                l.mode_id,
                ls.leaderboard_id,
                le.*,
                sup.steam_user_id,
                su.beampro_user_id,
                su.discord_user_id,
                su.reddit_user_id,
                su.twitch_user_id,
                su.twitter_user_id,
                su.youtube_user_id
            FROM leaderboard_snapshots ls 
            JOIN leaderboards l ON l.leaderboard_id = ls.leaderboard_id
            LEFT JOIN leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id
            JOIN leaderboard_entries_{$date->format('Y_m')} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            JOIN steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id
            JOIN steam_users su ON su.steam_user_id = sup.steam_user_id
            {{WHERE_CRITERIA}}
            ORDER BY ls.leaderboard_snapshot_id ASC
        ");
        
        $resultset->addFilterCriteria("ls.date = :date", array(
            ':date' => $date_formatted
        ));
        
        $resultset->addFilterCriteria("lb.leaderboards_blacklist_id IS NULL");
        
        $resultset->addFilterCriteria("l.is_daily = 0");

        $resultset->setAsCursor(100000);
        
        ExternalSites::loadAll();
        
        db()->beginTransaction();
        
        $transaction = cache()->transaction();
        
        $resultset->prepareExecuteQuery();
        
        $current_leaderboard_snapshot_id = NULL;
        $current_leaderboard_id = NULL;
        
        $entries = array();
        $leaderboard_entries = array();
        $indexes = array();
        
        do {
            $entries = $resultset->getNextCursorChunk();
        
            if(!empty($entries)) {
                foreach($entries as $entry) {
                    $leaderboard_id = (int)$entry['leaderboard_id'];
                    $leaderboard_snapshot_id = (int)$entry['leaderboard_snapshot_id'];
                    $steam_user_id = (int)$entry['steam_user_id'];
                    $release_id = (int)$entry['release_id'];
                    $mode_id = (int)$entry['mode_id'];
                
                    if($current_leaderboard_snapshot_id != $leaderboard_snapshot_id) {                        
                        if(!empty($current_leaderboard_snapshot_id)) {
                            static::saveIntoCache($date_formatted, $current_leaderboard_id, $transaction, $leaderboard_entries, $indexes);
                            
                            $leaderboard_entries = array();
                            $indexes = array();
                        }
                        
                        $current_leaderboard_snapshot_id = $leaderboard_snapshot_id;
                        $current_leaderboard_id = $leaderboard_id;
                    }
                    
                    $steam_user_pb_id = (int)$entry['steam_user_pb_id'];
                    $rank = (int)$entry['rank'];
                    
                    $leaderboard_entries[$steam_user_id] = implode(',', array(
                        $steam_user_pb_id,
                        $rank
                    ));
                    
                    $users_index_base_name = CacheNames::getIndexName($leaderboard_id, array());
                    
                    ExternalSites::addToSiteIdIndexes($indexes, $entry, $users_index_base_name, $steam_user_id, $rank);
                }
            }
        }
        while(!empty($entries));
        
        if(!empty($leaderboard_entries)) {
            static::saveIntoCache($date_formatted, $current_leaderboard_id, $transaction, $leaderboard_entries, $indexes);
        }
        
        $transaction->commit();
        
        db()->commit();
    }
    
    protected static function saveDailyIntoCache(DateTime $date, $transaction, &$entries, &$indexes) {
        if(!empty($entries)) {
            $date_formatted = $date->format('Y-m-d');
        
            $transaction->hSet(CacheNames::getDailyEntriesName(), $date_formatted, static::encodeRecord($entries));
                                
            if(!empty($indexes)) {
                foreach($indexes as $key => $index_data) {
                    $transaction->hSet($key, $date_formatted, static::encodeRecord($index_data));
                }
            }
        }
    }
    
    public static function loadDailiesIntoCache(DateTime $date) { 
        $date_formatted = $date->format('Y-m-d');
    
        $resultset = new SQL("daily_leaderboard_entries");
            
        $resultset->setBaseQuery("
            SELECT 
                l.release_id,
                l.mode_id,
                l.leaderboard_id,
                l.daily_date,
                le.*,
                sup.steam_user_id,
                su.beampro_user_id,
                su.discord_user_id,
                su.reddit_user_id,
                su.twitch_user_id,
                su.twitter_user_id,
                su.youtube_user_id
            FROM leaderboard_snapshots ls 
            JOIN leaderboards l ON l.leaderboard_id = ls.leaderboard_id
            LEFT JOIN leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id
            JOIN leaderboard_entries_{$date->format('Y_m')} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            JOIN steam_user_pbs sup ON sup.steam_user_pb_id = le.steam_user_pb_id
            JOIN steam_users su ON su.steam_user_id = sup.steam_user_id
            {{WHERE_CRITERIA}}
            ORDER BY ls.leaderboard_snapshot_id ASC
        ");
        
        $resultset->addFilterCriteria("ls.date = :date", array(
            ':date' => $date_formatted
        ));
        
        $resultset->addFilterCriteria("lb.leaderboards_blacklist_id IS NULL");
        
        $resultset->addFilterCriteria("l.is_daily_ranking = 1");
        $resultset->addFilterCriteria("l.is_score_run = 1");
        $resultset->addFilterCriteria("l.is_daily = 1");
        $resultset->addFilterCriteria("l.is_co_op = 0");

        $resultset->setAsCursor(100000);
        
        ExternalSites::loadAll();
        
        db()->beginTransaction();
        
        $transaction = cache()->transaction();
        
        $resultset->prepareExecuteQuery();
        
        $current_leaderboard_id = NULL;
        
        $entries = array();
        $leaderboard_entries = array();
        $indexes = array();
        
        do {
            $entries = $resultset->getNextCursorChunk();
        
            if(!empty($entries)) {
                foreach($entries as $entry) {
                    $leaderboard_id = (int)$entry['leaderboard_id'];
                    $steam_user_id = (int)$entry['steam_user_id'];
                    $release_id = (int)$entry['release_id'];
                    $mode_id = (int)$entry['mode_id'];
                
                    if($current_leaderboard_id != $leaderboard_id) {                        
                        if(!empty($current_leaderboard_id)) {
                            static::saveDailyIntoCache($date, $transaction, $leaderboard_entries, $indexes);
                            
                            $leaderboard_entries = array();
                            $indexes = array();
                        }
                        
                        $current_leaderboard_id = $leaderboard_id;
                    }
                    
                    $steam_user_pb_id = (int)$entry['steam_user_pb_id'];
                    $rank = (int)$entry['rank'];
                    
                    $leaderboard_entries[$steam_user_id] = implode(',', array(
                        $steam_user_pb_id,
                        $rank
                    ));
                    
                    $users_index_base_name = CacheNames::getDailyIndexName(array(
                        $release_id,
                        $mode_id
                    ));
                    
                    ExternalSites::addToSiteIdIndexes($indexes, $entry, $users_index_base_name, $steam_user_id, $rank);
                }
            }
        }
        while(!empty($entries));
        
        if(!empty($leaderboard_entries)) {
            static::saveDailyIntoCache($date, $transaction, $leaderboard_entries, $indexes);
        }
        
        $transaction->commit();
        
        db()->commit();
    }*/
}