<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\ExternalSites\Database\ExternalSites as DatabaseExternalSites;
use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
use \Modules\Necrolab\Models\Leaderboards\Database\Replays as DatabaseReplays;
use \Modules\Necrolab\Models\Leaderboards\Database\Details as DatabaseDetails;
use \Modules\Necrolab\Models\Leaderboards\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\Leaderboards\Database\Leaderboards as DatabaseLeaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\Snapshots as DatabaseSnapshots;
use \Modules\Necrolab\Models\Leaderboards\Entries as BaseEntries;

class Entries
extends BaseEntries {
    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("        
            CREATE TABLE leaderboard_entries_{$date_formatted} (
                leaderboard_snapshot_id integer NOT NULL,
                steam_user_id integer NOT NULL,
                score integer NOT NULL,
                rank integer NOT NULL,
                steam_replay_id integer NOT NULL,
                leaderboard_entry_details_id smallint NOT NULL,
                \"time\" double precision,
                is_win smallint NOT NULL DEFAULT 0,
                zone smallint,
                level smallint,
                win_count smallint,
                CONSTRAINT pk_leaderboard_entries_{$date_formatted}_leaderboard_entry_id PRIMARY KEY (leaderboard_snapshot_id, steam_user_id, rank),
                CONSTRAINT fk_leaderboard_entries_{$date_formatted}_leaderboard_snapshot_id FOREIGN KEY (leaderboard_snapshot_id)
                    REFERENCES leaderboard_snapshots (leaderboard_snapshot_id) MATCH SIMPLE
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_leaderboard_entries_{$date_formatted}_steam_user_id FOREIGN KEY (steam_user_id)
                    REFERENCES steam_users (steam_user_id) MATCH SIMPLE
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_leaderboard_entries_{$date_formatted}_steam_replay_id FOREIGN KEY (steam_replay_id)
                    REFERENCES steam_replays (steam_replay_id) MATCH SIMPLE
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_leaderboard_entries_{$date_formatted}_leaderboard_entry_details_id FOREIGN KEY (leaderboard_entry_details_id)
                    REFERENCES leaderboard_entry_details (leaderboard_entry_details_id) MATCH SIMPLE
                    ON UPDATE CASCADE ON DELETE CASCADE
            )
            WITH (
                OIDS=FALSE
            );

            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_leaderboard_snapshot_id
            ON leaderboard_entries_{$date_formatted}
            USING btree
            (leaderboard_snapshot_id);
            
            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_steam_user_id
            ON leaderboard_entries_{$date_formatted}
            USING btree
            (steam_user_id);
            
            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_steam_replay_id
            ON leaderboard_entries_{$date_formatted}
            USING btree
            (steam_replay_id);
            
            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_leaderboard_entry_details_id
            ON leaderboard_entries_{$date_formatted}
            USING btree
            (leaderboard_entry_details_id);
        ");
    }
    
    public static function clear($leaderboard_snapshot_id, DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->delete("leaderboard_entries_{$date_formatted}", array(
            'leaderboard_snapshot_id' => $leaderboard_snapshot_id
        ), array(), "leaderboard_entries_{$date_formatted}_delete");
    }

    public static function getAllBaseResultset(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        $resultset = new SQL("leaderboard_entries_{$date_formatted}");

        DatabaseLeaderboards::setSelectFields($resultset);
        DatabaseSnapshots::setSelectFields($resultset);
        DatabaseCharacters::setSelectFields($resultset);
        DatabaseEntry::setSelectFields($resultset);
        DatabaseReplays::setSelectFields($resultset);
        DatabaseDetails::setSelectFields($resultset);
        
        $resultset->setFromTable('leaderboards l');
        
        $resultset->addJoinCriteria('releases r ON r.release_id = l.release_id');
        $resultset->addJoinCriteria('characters c ON c.character_id = l.character_id');
        $resultset->addJoinCriteria('leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id');
        $resultset->addJoinCriteria("leaderboard_entries_{$date_formatted} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id");
        $resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = le.leaderboard_entry_details_id');
        $resultset->addLeftJoinCriteria("
            steam_replays sr ON sr.steam_replay_id = le.steam_replay_id
            AND sr.downloaded = 1
        ");

        $resultset->addFilterCriteria('ls.date = :date', array(
            ':date' => $date->format('Y-m-d')
        ));
        
        return $resultset;
    }
    
    public static function getPowerRankingsResultset($release_id, DateTime $date) {
        $resultset = static::getAllBaseResultset($date);
        
        $resultset->addFilterCriteria('l.release_id = :release_id', array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria('l.is_power_ranking = 1');
        
        return $resultset;
    }
    
    public static function getDailyRankingsResultset($release_id, DateTime $date) {
        $resultset = static::getAllBaseResultset($date);
        
        $resultset->addFilterCriteria('l.release_id = :release_id', array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria('l.daily_date = :daily_date', array(
            ':daily_date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('l.is_daily_ranking = 1');
        
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
        
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = le.steam_user_id');
        
        $resultset->addFilterCriteria('l.lbid = :lbid', array(
            ':lbid' => $lbid
        ));
        
        $resultset->addSortCriteria('le.rank', 'ASC');
        
        DatabaseExternalSites::addSiteUserLeftJoins($resultset);
        
        return $resultset;
    }
    
    public static function getApiAllDailyResultset($release_name, DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        $resultset = static::getAllBaseResultset($date);
    
        $resultset->setName("api_daily_leaderboard_entries_{$date_formatted}");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            ),
        ));
        
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = le.steam_user_id');
        
        $resultset->addFilterCriteria('l.daily_date = :daily_date', array(
            ':daily_date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('l.is_daily_ranking = 1');
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
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
        
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = le.steam_user_id');
        
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
        
        DatabaseEntry::setSelectFields($resultset);
        
        $resultset->setFromTable('leaderboards l');
        
        $resultset->addJoinCriteria('releases r ON r.release_id = l.release_id');
        $resultset->addJoinCriteria("
            leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            AND ls.date = l.daily_date
        ");
        $resultset->addJoinCriteria("{{PARTITION_TABLE}} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id");
        $resultset->addJoinCriteria('leaderboard_entry_details led ON led.leaderboard_entry_details_id = le.leaderboard_entry_details_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = le.steam_user_id');
        $resultset->addLeftJoinCriteria("
            steam_replays sr ON sr.steam_replay_id = le.steam_replay_id
            AND sr.downloaded = 1
        ");
        
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