<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;
use \Framework\Data\Database\InsertQueue;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis\Hybrid as HyrbidResultset;
use \Modules\Necrolab\Models\ExternalSites;
use \Modules\Necrolab\Models\Rankings\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\Rankings\Entries as BaseEntries;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\Characters;
use \Modules\Necrolab\Models\Rankings\Cache\CacheNames;
use \Modules\Necrolab\Models\SteamUsers\CacheNames as SteamUsersCacheNames;

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
        $base_index_name = "idx_power_ranking_entries_{$date_formatted}";
        
        db()->exec("DROP INDEX IF EXISTS {$base_index_name}_steam_user_id;");
    }
    
    public static function createPartitionTableIndexes(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        $base_index_name = "idx_power_ranking_entries_{$date_formatted}";
        
        db()->exec("
            CREATE INDEX IF NOT EXISTS {$base_index_name}_steam_user_id
            ON power_ranking_entries_{$date_formatted}
            USING btree (steam_user_id);
        ");
    }
    
    protected static function getCharacterColumnsSql() {
        $active_characters = Characters::getActive();
        
        $character_columns = array();
        
        if(!empty($active_characters)) {                    
            foreach($active_characters as $active_character) {
                $character_name = $active_character['name'];
                
                $character_columns[] = "{$character_name}_score_pb_id integer";
                $character_columns[] = "{$character_name}_score_rank integer";
                
                $character_columns[] = "{$character_name}_speed_pb_id integer";
                $character_columns[] = "{$character_name}_speed_rank integer";
                
                if($character_name != 'all' && $character_name != 'all_dlc' && $character_name != 'story') {
                    $character_columns[] = "{$character_name}_deathless_pb_id integer";
                    $character_columns[] = "{$character_name}_deathless_rank integer";
                }
                
                $character_columns[] = "{$character_name}_rank integer";
            }
        }
        
        $character_column_sql = implode(",\n", $character_columns);
        
        return $character_column_sql;
    }
    
    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        
        
        static::dropPartitionTableIndexes($date);
    
        db()->exec("        
            CREATE TABLE power_ranking_entries_{$date_formatted}
            (
                power_ranking_id integer NOT NULL,
                steam_user_id integer NOT NULL,
                " . static::getCharacterColumnsSql() . ",
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
        return new InsertQueue("power_ranking_entries", db(), 400);
    }
    
    public static function createTemporaryTable() {
        db()->exec("
            CREATE TEMPORARY TABLE power_ranking_entries (
                power_ranking_id integer NOT NULL,
                steam_user_id integer NOT NULL,
                " . static::getCharacterColumnsSql() . ",
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
    
    public static function getAllBaseResultset($release_id, $mode_id, $seeded, $external_site_id, DateTime $date) {  
        $date_formatted = $date->format('Y-m-d');
    
        $sql_resultset = new SQL('power_ranking_entries');
        
        $sql_resultset->setBaseQuery("
            {{SELECT_FIELDS}}
            FROM power_rankings pr
            JOIN power_ranking_entries_{$date->format('Y_m')} pre ON pre.power_ranking_id = pr.power_ranking_id
            JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
            {{JOIN_CRITERIA}}
            {{WHERE_CRITERIA}}
            {{ORDER_CRITERIA}}
        ");
        
        //These two calls need to be made in this order for optimal query speed
        ExternalSites::addSiteUserLeftJoins($sql_resultset);
        DatabaseEntry::setSelectFields($sql_resultset);
        
        $sql_resultset->addFilterCriteria('pr.date = ?', array(
            $date_formatted
        ));
        
        $sql_resultset->addFilterCriteria('pr.release_id = ?', array(
            $release_id
        ));
        
        $sql_resultset->addFilterCriteria('pr.mode_id = ?', array(
            $mode_id
        ));
        
        $sql_resultset->addFilterCriteria('pr.seeded = ?', array(
            $seeded
        ));
        
        $sql_resultset->setSortCriteria('pre.rank', 'ASC');
        
        $resultset = new HyrbidResultset('power_ranking_entries', cache('database'), cache('local'));
        
        $resultset->setSqlResultset($sql_resultset, 'pre.steam_user_id');
        
        $resultset->setIndexName(CacheNames::getIndexName(CacheNames::getPowerRankingName($release_id, $mode_id, $seeded), array(
            $external_site_id
        )));
        
        $resultset->setPartitionName($date_formatted);
        
        return $resultset;
    }
    
    public static function getAllScoreResultset($release_id, $mode_id, $seeded, $external_site_id, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_id, $mode_id, $seeded, $external_site_id, $date);
        
        $resultset->setIndexName(CacheNames::getIndexName(CacheNames::getScoreName($release_id, $mode_id, $seeded), array(
            $external_site_id
        )));
        
        $resultset->getSqlResultset()->setSortCriteria('pre.score_rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllSpeedResultset($release_id, $mode_id, $seeded, $external_site_id, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_id, $mode_id, $seeded, $external_site_id, $date);
        
        $resultset->setIndexName(CacheNames::getIndexName(CacheNames::getSpeedName($release_id, $mode_id, $seeded), array(
            $external_site_id
        )));
        
        $resultset->getSqlResultset()->setSortCriteria('pre.speed_rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllDeathlessResultset($release_id, $mode_id, $seeded, $external_site_id, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_id, $mode_id, $seeded, $external_site_id, $date);
        
        $resultset->setIndexName(CacheNames::getIndexName(CacheNames::getDeathlessName($release_id, $mode_id, $seeded), array(
            $external_site_id
        )));
        
        $resultset->getSqlResultset()->setSortCriteria('pre.deathless_rank', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllCharacterResultset($character_id, $character_name, $release_id, $mode_id, $seeded, $external_site_id, DateTime $date) { 
        $resultset = static::getAllBaseResultset($release_id, $mode_id, $seeded, $external_site_id, $date);
        
        $resultset->setIndexName(CacheNames::getIndexName(CacheNames::getCharacterName($release_id, $mode_id, $seeded, $character_id), array(
            $external_site_id
        )));
        
        $resultset->getSqlResultset()->setSortCriteria("pre.{$character_name}_rank", 'ASC');
        
        return $resultset;
    }
    
    public static function getSteamUserBaseResultset($release_id, $mode_id, $seeded, $steamid, DateTime $start_date, DateTime $end_date) {                    
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
        
        $resultset->addJoinCriteria('{{PARTITION_TABLE}} pre ON pre.power_ranking_id = pr.power_ranking_id');
        $resultset->addJoinCriteria('steam_users su ON su.steam_user_id = pre.steam_user_id');
        
        $parition_table_names = static::getPartitionTableNames('power_ranking_entries', $start_date, $end_date);
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $resultset->addFilterCriteria('pr.date BETWEEN ? AND ?', array(
            $start_date->format('Y-m-d'),
            $end_date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('pr.release_id = ?', array(
            $release_id
        ));
        
        $resultset->addFilterCriteria('pr.mode_id = ?', array(
            $mode_id
        ));
        
        $resultset->addFilterCriteria('pr.seeded = ?', array(
            $seeded
        ));
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->setSortCriteria('date', 'DESC');
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);        
        
        return $resultset;
    }
    
    public static function getSteamUserScoreResultset($release_id, $mode_id, $seeded, $steamid, DateTime $start_date, DateTime $end_date) { 
        $resultset = static::getSteamUserBaseResultset($release_id, $mode_id, $seeded, $steamid, $start_date, $end_date);
        
        $resultset->addFilterCriteria('pre.score_rank IS NOT NULL');
        
        $resultset->clearCountResultset();
        
        $count_resultset = clone $resultset;

        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);  
        
        return $resultset;
    }
    
    public static function getSteamUserSpeedResultset($release_id, $mode_id, $seeded, $steamid, DateTime $start_date, DateTime $end_date) { 
        $resultset = static::getSteamUserBaseResultset($release_id, $mode_id, $seeded, $steamid, $start_date, $end_date);
        
        $resultset->addFilterCriteria('pre.speed_rank IS NOT NULL');
        
        $resultset->clearCountResultset();
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);  
        
        return $resultset;
    }
    
    public static function getSteamUserDeathlessResultset($release_id, $mode_id, $seeded, $steamid, DateTime $start_date, DateTime $end_date) { 
        $resultset = static::getSteamUserBaseResultset($release_id, $mode_id, $seeded, $steamid, $start_date, $end_date);
        
        $resultset->addFilterCriteria('pre.deathless_rank IS NOT NULL');
        
        $resultset->clearCountResultset();
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);  
        
        return $resultset;
    }
    
    public static function getSteamUserCharacterResultset($character_name, $release_id, $mode_id, $seeded, $steamid, DateTime $start_date, DateTime $end_date) { 
        $resultset = static::getSteamUserBaseResultset($release_id, $mode_id, $seeded, $steamid, $start_date, $end_date);
        
        $resultset->addFilterCriteria("pre.{$character_name}_rank IS NOT NULL");
        
        $resultset->clearCountResultset();
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        
        $resultset->setCountResultset($count_resultset);  
        
        return $resultset;
    }
    
    public static function loadIntoCache(DateTime $date) {    
        $date_formatted = $date->format('Y-m-d');
        
        $active_characters = Characters::getActive();
    
        $resultset = new SQL("power_ranking_entries_cache");
        
        $resultset->setBaseQuery("
            {{SELECT_FIELDS}}
            FROM power_rankings pr 
            JOIN power_ranking_entries_{$date->format('Y_m')} pre ON pre.power_ranking_id = pr.power_ranking_id
            JOIN steam_users su ON su.steam_user_id = pre.steam_user_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'pr.release_id',
                'alias' => 'release_id'
            ),
            array(
                'field' => 'pr.mode_id',
                'alias' => 'mode_id'
            ),
            array(
                'field' => 'pr.seeded',
                'alias' => 'seeded'
            ),
            array(
                'field' => 'pre.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'pre.rank',
                'alias' => 'rank'
            ),
            array(
                'field' => 'pre.score_rank',
                'alias' => 'score_rank'
            ),
            array(
                'field' => 'pre.deathless_rank',
                'alias' => 'deathless_rank'
            ),
            array(
                'field' => 'pre.speed_rank',
                'alias' => 'speed_rank'
            )
        ));
        
        if(!empty($active_characters)) {
            foreach($active_characters as $active_character) {
                $character_rank_name = "{$active_character['name']}_rank";
            
                $resultset->addSelectField("pre.{$character_rank_name}", $character_rank_name);
            }
        }
        
        ExternalSites::addSiteIdSelectFields($resultset);
        
        $resultset->addFilterCriteria("pr.date = :date", array(
            ':date' => $date_formatted
        ));

        $resultset->setAsCursor(10000);
        
        db()->beginTransaction();
        
        $resultset->prepareExecuteQuery();
        
        $entries = array();
        $indexes = array();
        
        do {
            $entries = $resultset->getNextCursorChunk();
        
            if(!empty($entries)) {
                foreach($entries as $entry) {
                    $steam_user_id = (int)$entry['steam_user_id'];
                    $release_id = (int)$entry['release_id'];
                    $mode_id = (int)$entry['mode_id'];
                    $seeded = (int)$entry['seeded'];                   
                    
                    $rank = (int)$entry['rank']; 
                    
                    ExternalSites::addToSiteIdIndexes(
                        $indexes, 
                        $entry, 
                        CacheNames::getIndexName(CacheNames::getPowerRankingName($release_id, $mode_id, $seeded), array()), 
                        $steam_user_id,
                        $rank
                    );
                    
                    $score_rank = NULL;
                
                    if(isset($entry['score_rank'])) {
                        $score_rank = (int)$entry['score_rank'];
                        
                        ExternalSites::addToSiteIdIndexes(
                            $indexes, 
                            $entry, 
                            CacheNames::getIndexName(CacheNames::getScoreName($release_id, $mode_id, $seeded), array()), 
                            $steam_user_id,
                            $score_rank
                        );
                    }
                    
                    $deathless_rank = NULL;
                    
                    if(isset($entry['deathless_rank'])) {
                        $deathless_rank = (int)$entry['deathless_rank'];
                        
                        ExternalSites::addToSiteIdIndexes(
                            $indexes, 
                            $entry, 
                            CacheNames::getIndexName(CacheNames::getDeathlessName($release_id, $mode_id, $seeded), array()), 
                            $steam_user_id,
                            $deathless_rank
                        );
                    }
                    
                    $speed_rank = NULL;
                    
                    if(isset($entry['speed_rank'])) {
                        $speed_rank = (int)$entry['speed_rank'];
                        
                        ExternalSites::addToSiteIdIndexes(
                            $indexes, 
                            $entry, 
                            CacheNames::getIndexName(CacheNames::getSpeedName($release_id, $mode_id, $seeded), array()), 
                            $steam_user_id,
                            $speed_rank
                        );
                    }
                    
                    if(!empty($active_characters)) {                
                        foreach($active_characters as $character) {
                            $character_name = $character['name'];
                            $character_id = $character['character_id'];
                            
                            $character_rank = NULL;
                            $rank_name = "{$character_name}_rank";
                            
                            if(!empty($entry[$rank_name])) {
                                $character_rank = (int)$entry[$rank_name];
                            }
                            
                            if(!empty($character_rank)) {
                                ExternalSites::addToSiteIdIndexes(
                                    $indexes, 
                                    $entry, 
                                    CacheNames::getIndexName(CacheNames::getCharacterName($release_id, $mode_id, $seeded, $character_id), array()), 
                                    $steam_user_id,
                                    $character_rank
                                );
                            }
                        }
                    }
                }
            }
        }
        while(!empty($entries));
        
        db()->commit();
        
        $transaction = cache('database')->transaction();
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                ksort($index_data);
            
                $transaction->set($date_formatted, static::encodeRecord($index_data), $key);
            }
        }
        
        $transaction->commit();
    }
}