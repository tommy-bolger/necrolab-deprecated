<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\ExternalSites\Database\ExternalSites as DatabaseExternalSites;
use \Modules\Necrolab\Models\Rankings\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\Rankings\Entries as BaseEntries;

class Entries
extends BaseEntries {
    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("        
            CREATE TABLE power_ranking_entries_{$date_formatted}
            (
                power_ranking_id integer NOT NULL,
                steam_user_id integer NOT NULL,
                cadence_score_rank integer,
                cadence_score_rank_points double precision,
                cadence_score integer,
                cadence_deathless_rank integer,
                cadence_deathless_rank_points double precision,
                cadence_deathless_win_count smallint,
                cadence_speed_rank integer,
                cadence_speed_rank_points double precision,
                cadence_speed_time double precision,
                cadence_rank integer,
                cadence_rank_points double precision,
                bard_score_rank integer,
                bard_score_rank_points double precision,
                bard_score integer,
                bard_deathless_rank integer,
                bard_deathless_rank_points double precision,
                bard_deathless_win_count smallint,
                bard_speed_rank integer,
                bard_speed_rank_points double precision,
                bard_speed_time double precision,
                bard_rank integer,
                bard_rank_points double precision,
                monk_score_rank integer,
                monk_score_rank_points double precision,
                monk_score integer,
                monk_deathless_rank integer,
                monk_deathless_rank_points double precision,
                monk_deathless_win_count smallint,
                monk_speed_rank integer,
                monk_speed_rank_points double precision,
                monk_speed_time double precision,
                monk_rank integer,
                monk_rank_points double precision,
                aria_score_rank integer,
                aria_score_rank_points double precision,
                aria_score integer,
                aria_deathless_rank integer,
                aria_deathless_rank_points double precision,
                aria_deathless_win_count smallint,
                aria_speed_rank integer,
                aria_speed_rank_points double precision,
                aria_speed_time double precision,
                aria_rank integer,
                aria_rank_points double precision,
                bolt_score_rank integer,
                bolt_score_rank_points double precision,
                bolt_score integer,
                bolt_deathless_rank integer,
                bolt_deathless_rank_points double precision,
                bolt_deathless_win_count smallint,
                bolt_speed_rank integer,
                bolt_speed_rank_points double precision,
                bolt_speed_time double precision,
                bolt_rank integer,
                bolt_rank_points double precision,
                dove_score_rank integer,
                dove_score_rank_points double precision,
                dove_score integer,
                dove_deathless_rank integer,
                dove_deathless_rank_points double precision,
                dove_deathless_win_count smallint,
                dove_speed_rank integer,
                dove_speed_rank_points double precision,
                dove_speed_time double precision,
                dove_rank integer,
                dove_rank_points double precision,
                eli_score_rank integer,
                eli_score_rank_points double precision,
                eli_score integer,
                eli_deathless_rank integer,
                eli_deathless_rank_points double precision,
                eli_deathless_win_count smallint,
                eli_speed_rank integer,
                eli_speed_rank_points double precision,
                eli_speed_time double precision,
                eli_rank integer,
                eli_rank_points double precision,
                melody_score_rank integer,
                melody_score_rank_points double precision,
                melody_score integer,
                melody_deathless_rank integer,
                melody_deathless_rank_points double precision,
                melody_deathless_win_count smallint,
                melody_speed_rank integer,
                melody_speed_rank_points double precision,
                melody_speed_time double precision,
                melody_rank integer,
                melody_rank_points double precision,
                dorian_score_rank integer,
                dorian_score_rank_points double precision,
                dorian_score integer,
                dorian_deathless_rank integer,
                dorian_deathless_rank_points double precision,
                dorian_deathless_win_count smallint,
                dorian_speed_rank integer,
                dorian_speed_rank_points double precision,
                dorian_speed_time double precision,
                dorian_rank integer,
                dorian_rank_points double precision,
                coda_score_rank integer,
                coda_score_rank_points double precision,
                coda_score integer,
                coda_deathless_rank integer,
                coda_deathless_rank_points double precision,
                coda_deathless_win_count smallint,
                coda_speed_rank integer,
                coda_speed_rank_points double precision,
                coda_speed_time double precision,
                coda_rank integer,
                coda_rank_points double precision,
                nocturna_score_rank integer,
                nocturna_score_rank_points double precision,
                nocturna_score integer,
                nocturna_deathless_rank integer,
                nocturna_deathless_rank_points double precision,
                nocturna_deathless_win_count smallint,
                nocturna_speed_rank integer,
                nocturna_speed_rank_points double precision,
                nocturna_speed_time double precision,
                nocturna_rank integer,
                nocturna_rank_points double precision,
                all_score_rank integer,
                all_score_rank_points double precision,
                all_score integer,
                all_speed_rank integer,
                all_speed_rank_points double precision,
                all_speed_time double precision,
                all_rank integer,
                all_rank_points double precision,
                story_score_rank integer,
                story_score_rank_points double precision,
                story_score integer,
                story_speed_rank integer,
                story_speed_rank_points double precision,
                story_speed_time double precision,
                story_rank integer,
                story_rank_points double precision,
                score_total integer,
                score_rank integer,
                score_rank_points_total double precision,
                deathless_total_win_count smallint,
                deathless_rank integer,
                deathless_rank_points_total double precision,
                speed_total_time double precision,
                speed_rank integer,
                speed_rank_points_total double precision,
                rank integer NOT NULL,
                total_points double precision,
                CONSTRAINT pk_power_ranking_entries_{$date_formatted}_power_ranking_entry_id PRIMARY KEY (power_ranking_id, steam_user_id),
                CONSTRAINT fk_power_ranking_entries_{$date_formatted}_power_ranking_id FOREIGN KEY (power_ranking_id)
                    REFERENCES power_rankings (power_ranking_id) MATCH SIMPLE
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_power_ranking_entries_{$date_formatted}_steam_user_id FOREIGN KEY (steam_user_id)
                    REFERENCES steam_users (steam_user_id) MATCH SIMPLE
                    ON UPDATE CASCADE ON DELETE CASCADE
            )
            WITH (
                OIDS=FALSE
            );

            CREATE INDEX idx_power_ranking_entries_{$date_formatted}_deathless_rank_desc
            ON power_ranking_entries_{$date_formatted}
            USING btree
            (deathless_rank DESC);

            CREATE INDEX idx_power_ranking_entries_{$date_formatted}_power_ranking_id
            ON power_ranking_entries_{$date_formatted}
            USING btree
            (power_ranking_id);

            CREATE INDEX idx_power_ranking_entries_{$date_formatted}_rank_asc
            ON power_ranking_entries_{$date_formatted}
            USING btree
            (rank);

            CREATE INDEX idx_power_ranking_entries_{$date_formatted}_rank_desc
            ON power_ranking_entries_{$date_formatted}
            USING btree
            (rank DESC);

            CREATE INDEX idx_power_ranking_entries_{$date_formatted}_score_rank_desc
            ON power_ranking_entries_{$date_formatted}
            USING btree
            (score_rank DESC);

            CREATE INDEX idx_power_ranking_entries_{$date_formatted}_speed_rank_asc
            ON power_ranking_entries_{$date_formatted}
            USING btree
            (speed_rank);

            CREATE INDEX idx_power_ranking_entries_{$date_formatted}_steam_user_id
            ON power_ranking_entries_{$date_formatted}
            USING btree
            (steam_user_id);
        ");
    }
    
    public static function clear($power_ranking_id, DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->delete("power_ranking_entries_{$date_formatted}", array(
            'power_ranking_id' => $power_ranking_id
        ));
    }
    
    public static function getAllBaseResultset($release_name, DateTime $date) {    
        $resultset = new SQL('power_ranking_entries');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            ),
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            )
        ));
        
        DatabaseEntry::setSelectFields($resultset);
        
        $resultset->setFromTable('power_rankings pr');
        
        $resultset->addJoinCriteria('releases r ON r.release_id = pr.release_id');
        $resultset->addJoinCriteria("power_ranking_entries_{$date->format('Y_m')} pre ON pre.power_ranking_id = pr.power_ranking_id");
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = pre.steam_user_id');
        
        $resultset->addFilterCriteria('pr.date = :date', array(
            ':date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->setSortCriteria('pre.rank', 'ASC');
        
        DatabaseExternalSites::addSiteUserLeftJoins($resultset);
        
        return $resultset;
    }
    
    public static function getAllScoreResultset($release_name, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_name, $date);
        
        $resultset->addFilterCriteria('pre.score_rank IS NOT NULL');
        $resultset->setSortCriteria('pre.score_rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllSpeedResultset($release_name, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_name, $date);
        
        $resultset->addFilterCriteria('pre.speed_rank IS NOT NULL');
        $resultset->setSortCriteria('pre.speed_rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllDeathlessResultset($release_name, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_name, $date);
        
        $resultset->addFilterCriteria('pre.deathless_rank IS NOT NULL');
        $resultset->setSortCriteria('pre.deathless_rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllCharacterResultset($character_name, $release_name, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_name, $date);
        
        $resultset->addFilterCriteria("pre.{$character_name}_rank IS NOT NULL");
        $resultset->setSortCriteria("pre.{$character_name}_rank", 'ASC');
        
        return $resultset;
    }
    
    public static function getSteamUserBaseResultset($release_name, $steamid, DateTime $start_date, DateTime $end_date) {                    
        $resultset = new SQL('steam_user_power_ranking_entries');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'pr.date',
                'alias' => 'date'
            ),
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            )
        ));
        
        DatabaseEntry::setSelectFields($resultset);
        
        $resultset->setFromTable('power_rankings pr');
        
        $resultset->addJoinCriteria('releases r ON r.release_id = pr.release_id');
        $resultset->addJoinCriteria('{{PARTITION_TABLE}} pre ON pre.power_ranking_id = pr.power_ranking_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = pre.steam_user_id');
        
        $parition_table_names = static::getPartitionTableNames('power_ranking_entries', $start_date, $end_date);
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->addFilterCriteria('pr.date BETWEEN ? AND ?', array(
            $start_date->format('Y-m-d'),
            $end_date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('r.name = ?', array(
            $release_name
        ));

        $resultset->setSortCriteria('date', 'DESC');
        
        return $resultset;
    }
    
    public static function getSteamUserScoreResultset($release_name, $steamid, DateTime $start_date, DateTime $end_date) { 
        $resultset = static::getSteamUserBaseResultset($release_name, $steamid, $start_date, $end_date);
        
        $resultset->addFilterCriteria('pre.score_rank IS NOT NULL');
        
        return $resultset;
    }
    
    public static function getSteamUserSpeedResultset($release_name, $steamid, DateTime $start_date, DateTime $end_date) { 
        $resultset = static::getSteamUserBaseResultset($release_name, $steamid, $start_date, $end_date);
        
        $resultset->addFilterCriteria('pre.speed_rank IS NOT NULL');
        
        return $resultset;
    }
    
    public static function getSteamUserDeathlessResultset($release_name, $steamid, DateTime $start_date, DateTime $end_date) { 
        $resultset = static::getSteamUserBaseResultset($release_name, $steamid, $start_date, $end_date);
        
        $resultset->addFilterCriteria('pre.deathless_rank IS NOT NULL');
        
        return $resultset;
    }
    
    public static function getSteamUserCharacterResultset($character_name, $release_name, $steamid, DateTime $start_date, DateTime $end_date) { 
        $resultset = static::getSteamUserBaseResultset($release_name, $steamid, $start_date, $end_date);
        
        $resultset->addFilterCriteria("pre.{$character_name}_rank IS NOT NULL");
        
        return $resultset;
    }
}