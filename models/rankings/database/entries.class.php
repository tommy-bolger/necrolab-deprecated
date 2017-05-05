<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Framework\Data\Database\InsertQueue;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\ExternalSites\Database\ExternalSites as DatabaseExternalSites;
use \Modules\Necrolab\Models\Rankings\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\Rankings\Entries as BaseEntries;
use \Modules\Necrolab\Models\Modes\Database\Modes as DatabaseModes;

class Entries
extends BaseEntries {
    public static function dropPartitionTableConstraints(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("
            ALTER TABLE power_ranking_entries_{$date_formatted}
            DROP CONSTRAINT fk_power_ranking_entries_{$date_formatted}_power_ranking_id,
            DROP CONSTRAINT fk_power_ranking_entries_{$date_formatted}_steam_user_id;
        ");
    }
    
    public static function createPartitionTableConstraints(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        db()->exec("
            ALTER TABLE power_ranking_entries_{$date_formatted}
            ADD CONSTRAINT fk_power_ranking_entries_{$date_formatted}_power_ranking_id FOREIGN KEY (power_ranking_id)
                REFERENCES power_rankings (power_ranking_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT fk_power_ranking_entries_{$date_formatted}_steam_user_id FOREIGN KEY (steam_user_id)
                REFERENCES steam_users (steam_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE;
        ");
    }

    public static function dropPartitionTableIndexes(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        db()->exec("
            DROP INDEX IF EXISTS idx_power_ranking_entries_{$date_formatted}_power_ranking_id;
            DROP INDEX IF EXISTS idx_power_ranking_entries_{$date_formatted}_steam_user_id;
        ");
    }
    
    public static function createPartitionTableIndexes(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("
            CREATE INDEX idx_power_ranking_entries_{$date_formatted}_power_ranking_id
            ON power_ranking_entries_{$date_formatted}
            USING btree (power_ranking_id);

            CREATE INDEX idx_power_ranking_entries_{$date_formatted}_steam_user_id
            ON power_ranking_entries_{$date_formatted}
            USING btree (steam_user_id);
        ");
    }
    
    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        static::dropPartitionTableIndexes($date);
    
        db()->exec("        
            CREATE TABLE power_ranking_entries_{$date_formatted}
            (
                power_ranking_id integer NOT NULL,
                steam_user_id integer NOT NULL,
                cadence_score_pb_id integer,
                cadence_score_rank integer,
                cadence_deathless_pb_id integer,
                cadence_deathless_rank integer,
                cadence_speed_pb_id integer,
                cadence_speed_rank integer,
                cadence_rank integer,
                bard_score_pb_id integer,
                bard_score_rank integer,
                bard_deathless_pb_id integer,
                bard_deathless_rank integer,
                bard_speed_pb_id integer,
                bard_speed_rank integer,
                bard_rank integer,
                monk_score_pb_id integer,
                monk_score_rank integer,
                monk_deathless_pb_id integer,
                monk_deathless_rank integer,
                monk_speed_pb_id integer,
                monk_speed_rank integer,
                monk_rank integer,
                aria_score_pb_id integer,
                aria_score_rank integer,
                aria_deathless_pb_id integer,
                aria_deathless_rank integer,
                aria_speed_pb_id integer,
                aria_speed_rank integer,
                aria_rank integer,
                bolt_score_pb_id integer,
                bolt_score_rank integer,
                bolt_deathless_pb_id integer,
                bolt_deathless_rank integer,
                bolt_speed_pb_id integer,
                bolt_speed_rank integer,
                bolt_rank integer,
                dove_score_pb_id integer,
                dove_score_rank integer,
                dove_deathless_pb_id integer,
                dove_deathless_rank integer,
                dove_speed_pb_id integer,
                dove_speed_rank integer,
                dove_rank integer,
                eli_score_pb_id integer,
                eli_score_rank integer,
                eli_deathless_pb_id integer,
                eli_deathless_rank integer,
                eli_speed_pb_id integer,
                eli_speed_rank integer,
                eli_rank integer,
                melody_score_pb_id integer,
                melody_score_rank integer,
                melody_deathless_pb_id integer,
                melody_deathless_rank integer,
                melody_speed_pb_id integer,
                melody_speed_rank integer,
                melody_rank integer,
                dorian_score_pb_id integer,
                dorian_score_rank integer,
                dorian_deathless_pb_id integer,
                dorian_deathless_rank integer,
                dorian_speed_pb_id integer,
                dorian_speed_rank integer,
                dorian_rank integer,
                coda_score_pb_id integer,
                coda_score_rank integer,
                coda_deathless_pb_id integer,
                coda_deathless_rank integer,
                coda_speed_pb_id integer,
                coda_speed_rank integer,
                coda_rank integer,
                nocturna_score_pb_id integer,
                nocturna_score_rank integer,
                nocturna_deathless_pb_id integer,
                nocturna_deathless_rank integer,
                nocturna_speed_pb_id integer,
                nocturna_speed_rank integer,
                nocturna_rank integer,
                diamond_score_pb_id integer,
                diamond_score_rank integer,
                diamond_deathless_pb_id integer,
                diamond_deathless_rank integer,
                diamond_speed_pb_id integer,
                diamond_speed_rank integer,
                diamond_rank integer,
                all_score_pb_id integer,
                all_score_rank integer,
                all_speed_pb_id integer,
                all_speed_rank integer,
                all_rank integer,
                story_score_pb_id integer,
                story_score_rank integer,
                story_speed_pb_id integer,
                story_speed_rank integer,
                story_rank integer,
                score_rank integer,
                deathless_rank integer,
                speed_rank integer,
                rank integer NOT NULL,
                CONSTRAINT pk_power_ranking_entries_{$date_formatted}_power_ranking_entry_id PRIMARY KEY (power_ranking_id, steam_user_id)
            )
            WITH (
                OIDS=FALSE
            );
        ");
        
        static::createPartitionTableConstraints($date);
        static::createPartitionTableIndexes($date);
    }
    
    public static function clear($power_ranking_id, DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->delete("power_ranking_entries_{$date_formatted}", array(
            'power_ranking_id' => $power_ranking_id
        ));
    }
    
    public static function getInsertQueue(DateTime $date) {
        return new InsertQueue("power_ranking_entries_{$date->format('Y_m')}", db(), 600);
    }
    
    public static function vacuum(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("VACUUM ANALYZE power_ranking_entries_{$date_formatted};");
    }
    
    public static function getTempInsertQueue() {
        return new InsertQueue("power_ranking_entries", db(), 600);
    }
    
    public static function createTemporaryTable() {
        db()->exec("
            CREATE TEMPORARY TABLE power_ranking_entries (
                power_ranking_id integer NOT NULL,
                steam_user_id integer NOT NULL,
                cadence_score_pb_id integer,
                cadence_score_rank integer,
                cadence_deathless_pb_id integer,
                cadence_deathless_rank integer,
                cadence_speed_pb_id integer,
                cadence_speed_rank integer,
                cadence_rank integer,
                bard_score_pb_id integer,
                bard_score_rank integer,
                bard_deathless_pb_id integer,
                bard_deathless_rank integer,
                bard_speed_pb_id integer,
                bard_speed_rank integer,
                bard_rank integer,
                monk_score_pb_id integer,
                monk_score_rank integer,
                monk_deathless_pb_id integer,
                monk_deathless_rank integer,
                monk_speed_pb_id integer,
                monk_speed_rank integer,
                monk_rank integer,
                aria_score_pb_id integer,
                aria_score_rank integer,
                aria_deathless_pb_id integer,
                aria_deathless_rank integer,
                aria_speed_pb_id integer,
                aria_speed_rank integer,
                aria_rank integer,
                bolt_score_pb_id integer,
                bolt_score_rank integer,
                bolt_deathless_pb_id integer,
                bolt_deathless_rank integer,
                bolt_speed_pb_id integer,
                bolt_speed_rank integer,
                bolt_rank integer,
                dove_score_pb_id integer,
                dove_score_rank integer,
                dove_deathless_pb_id integer,
                dove_deathless_rank integer,
                dove_speed_pb_id integer,
                dove_speed_rank integer,
                dove_rank integer,
                eli_score_pb_id integer,
                eli_score_rank integer,
                eli_deathless_pb_id integer,
                eli_deathless_rank integer,
                eli_speed_pb_id integer,
                eli_speed_rank integer,
                eli_rank integer,
                melody_score_pb_id integer,
                melody_score_rank integer,
                melody_deathless_pb_id integer,
                melody_deathless_rank integer,
                melody_speed_pb_id integer,
                melody_speed_rank integer,
                melody_rank integer,
                dorian_score_pb_id integer,
                dorian_score_rank integer,
                dorian_deathless_pb_id integer,
                dorian_deathless_rank integer,
                dorian_speed_pb_id integer,
                dorian_speed_rank integer,
                dorian_rank integer,
                coda_score_pb_id integer,
                coda_score_rank integer,
                coda_deathless_pb_id integer,
                coda_deathless_rank integer,
                coda_speed_pb_id integer,
                coda_speed_rank integer,
                coda_rank integer,
                nocturna_score_pb_id integer,
                nocturna_score_rank integer,
                nocturna_deathless_pb_id integer,
                nocturna_deathless_rank integer,
                nocturna_speed_pb_id integer,
                nocturna_speed_rank integer,
                nocturna_rank integer,
                diamond_score_pb_id integer,
                diamond_score_rank integer,
                diamond_deathless_pb_id integer,
                diamond_deathless_rank integer,
                diamond_speed_pb_id integer,
                diamond_speed_rank integer,
                diamond_rank integer,
                all_score_pb_id integer,
                all_score_rank integer,
                all_speed_pb_id integer,
                all_speed_rank integer,
                all_rank integer,
                story_score_pb_id integer,
                story_score_rank integer,
                story_speed_pb_id integer,
                story_speed_rank integer,
                story_rank integer,
                score_rank integer,
                deathless_rank integer,
                speed_rank integer,
                rank integer NOT NULL
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveTempEntries(DateTime $date) {
        db()->exec("
            INSERT INTO power_ranking_entries_{$date->format('Y_m')}
            SELECT *
            FROM power_ranking_entries
        ");
    }    
    
    public static function getAllBaseResultset($release_name, $mode_name, DateTime $date) {    
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
        
        $resultset->setFromTable('power_rankings pr');
        
        $resultset->addJoinCriteria('releases r ON r.release_id = pr.release_id');
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = pr.mode_id');
        $resultset->addJoinCriteria("power_ranking_entries_{$date->format('Y_m')} pre ON pre.power_ranking_id = pr.power_ranking_id");
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = pre.steam_user_id');
        
        //These two calls need to be made in this order for optimal query speed
        DatabaseExternalSites::addSiteUserLeftJoins($resultset);
        DatabaseEntry::setSelectFields($resultset);
        
        $resultset->addFilterCriteria('pr.date = :date', array(
            ':date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->addFilterCriteria('mo.name = :mode_name', array(
            ':mode_name' => $mode_name
        ));
        
        $resultset->setSortCriteria('pre.rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllScoreResultset($release_name, $mode_name, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_name, $mode_name, $date);
        
        $resultset->addFilterCriteria('pre.score_rank IS NOT NULL');
        $resultset->setSortCriteria('pre.score_rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllSpeedResultset($release_name, $mode_name, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_name, $mode_name, $date);
        
        $resultset->addFilterCriteria('pre.speed_rank IS NOT NULL');
        $resultset->setSortCriteria('pre.speed_rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllDeathlessResultset($release_name, $mode_name, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_name, $mode_name, $date);
        
        $resultset->addFilterCriteria('pre.deathless_rank IS NOT NULL');
        $resultset->setSortCriteria('pre.deathless_rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllCharacterResultset($character_name, $release_name, $mode_name, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_name, $mode_name, $date);
        
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
            ),
            array(
                'field' => 'mo.sort_order',
                'alias' => 'mode_sort_order'
            )
        ));
        
        DatabaseModes::setSelectFields($resultset);
        
        $resultset->setFromTable('power_rankings pr');
        
        $resultset->addJoinCriteria('releases r ON r.release_id = pr.release_id');
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = pr.mode_id');
        $resultset->addJoinCriteria('{{PARTITION_TABLE}} pre ON pre.power_ranking_id = pr.power_ranking_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = pre.steam_user_id');
        
        DatabaseEntry::setSelectFields($resultset);
        
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
        $resultset->addSortCriteria('mode_sort_order', 'ASC');
        
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