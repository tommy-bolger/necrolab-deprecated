<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
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

            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_rank
            ON leaderboard_entries_{$date_formatted}
            USING btree
            (rank);

            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_score
            ON leaderboard_entries_{$date_formatted}
            USING btree
            (score);

            CREATE INDEX idx_leaderboard_entries_{$date_formatted}_time
            ON leaderboard_entries_{$date_formatted}
            USING btree
            (\"time\");
        ");
    }
    
    public static function clear($leaderboard_snapshot_id, DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->delete("leaderboard_entries_{$date_formatted}", array(
            'leaderboard_snapshot_id' => $leaderboard_snapshot_id
        ), array(), "leaderboard_entries_{$date_formatted}_delete");
    }

    public static function getEntriesResultset(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        $resultset = new SQL("leaderboard_entries_{$date_formatted}");
        
        $resultset->setBaseQuery("
            SELECT 
                l.lbid,
                l.daily_date, 
                l.character_id,
                le.steam_user_id,
                le.score,
                le.time,
                le.rank,
                le.is_win,
                le.zone,
                le.level,
                le.win_count,
                sr.ugcid,
                sr.seed,
                let.details,
                l.leaderboard_id
            FROM leaderboards l
            JOIN leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            JOIN leaderboard_entries_{$date_formatted} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            JOIN steam_replays sr ON sr.steam_replay_id = le.steam_replay_id
            JOIN leaderboard_entry_details let ON let.leaderboard_entry_details_id = le.leaderboard_entry_details_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('ls.date = :date', array(
            ':date' => $date->format('Y-m-d')
        ));
        
        return $resultset;
    }
    
    public static function getPowerRankingsResultset($release_id, DateTime $date) {
        $resultset = static::getEntriesResultset($date);
        
        $resultset->addFilterCriteria('l.release_id = :release_id', array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria('l.is_power_ranking = 1');
        
        return $resultset;
    }
    
    public static function getDailyRankingsResultset($release_id, DateTime $date) {
        $resultset = static::getEntriesResultset($date);
        
        $resultset->addFilterCriteria('l.release_id = :release_id', array(
            ':release_id' => $release_id
        ));
        
        $resultset->addFilterCriteria('l.daily_date = :daily_date', array(
            ':daily_date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('l.is_daily_ranking = 1');
        
        return $resultset;
    }
    
    public static function getEntriesPartitionResultset(DateTime $start_date, DateTime $end_date) {
        $partition_table_names = static::getPartitionTableNames('leaderboard_entries', $start_date, $end_date);
    
        $resultset = new SQL("leaderboard_entries_{$date_formatted}");
        
        $resultset->setBaseQuery("
            SELECT 
                l.lbid,
                l.daily_date, 
                l.character_id,
                le.steam_user_id,
                le.score,
                le.time,
                le.rank,
                le.is_win,
                le.zone,
                le.level,
                le.win_count,
                l.leaderboard_id
            FROM leaderboards l
            JOIN leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            JOIN {{PARTITION_TABLE}} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('ls.date BETWEEN :start_date AND :end_date', array(
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ));
        
        return $resultset;
    }
    
    public static function getDailyRankingsPartitionResultset(DateTime $start_date, DateTime $end_date) {
        $resultset = static::getEntriesPartitionResultset($start_date, $end_date);
        
        $resultset->addFilterCriteria('l.is_daily_ranking = 1');
        
        return $resultset;
    }
    
    public static function getEntriesDisplayResultset(DateTime $date, $lbid) {
        $date_formatted = $date->format('Y_m');
    
        $resultset = new SQL("display_leaderboard_entries_{$date_formatted}");
        
        $resultset->setBaseQuery("
            SELECT 
                l.lbid,
                l.daily_date, 
                l.character_id,
                le.steam_user_id,
                le.score,
                le.time,
                le.rank,
                le.is_win,
                le.zone,
                le.level,
                le.win_count,
                sr.ugcid,
                sr.seed,
                led.details,
                l.leaderboard_id,
                su.steamid,
                su.personaname,
                su.twitch_username,
                su.twitter_username,
                su.nico_nico_url,
                su.hitbox_username,
                su.website
            FROM leaderboards l
            JOIN leaderboard_snapshots ls ON ls.leaderboard_id = l.leaderboard_id
            JOIN leaderboard_entries_{$date_formatted} le ON le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            JOIN leaderboard_entry_details let ON let.leaderboard_entry_details_id = le.leaderboard_entry_details_id
            JOIN steam_users su ON su.steam_user_id = le.steam_user_id
            JOIN leaderboard_entry_details led on led.leaderboard_entry_details_id = le.leaderboard_entry_details_id
            LEFT JOIN steam_replays sr ON sr.steam_replay_id = le.steam_replay_id
                AND sr.downloaded = 1
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('ls.date = :date', array(
            ':date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('l.lbid = :lbid', array(
            ':lbid' => $lbid
        ));
        
        $resultset->addSortCriteria('le.rank', 'ASC');
        
        return $resultset;
    }
}