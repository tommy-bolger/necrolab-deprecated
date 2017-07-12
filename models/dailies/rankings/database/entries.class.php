<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \DateTime;
use \Framework\Data\Database\InsertQueue;
use \Framework\Data\ResultSet\SQL;
use \Framework\Data\ResultSet\Redis\Hybrid as HyrbidResultset;
use \Modules\Necrolab\Models\Modes;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\ExternalSites;
use \Modules\Necrolab\Models\Dailies\Rankings\Entries as BaseEntries;
use \Modules\Necrolab\Models\Dailies\Rankings\Cache\CacheNames;

class Entries
extends BaseEntries {    
    public static function dropPartitionTableConstraints(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("
            ALTER TABLE daily_ranking_entries_{$date_formatted}
            DROP CONSTRAINT fk_daily_ranking_entries_{$date_formatted}_daily_ranking_id,
            DROP CONSTRAINT fk_daily_ranking_entries_{$date_formatted}_steam_user_id;
        ");
    }
    
    public static function createPartitionTableConstraints(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        db()->exec("
            ALTER TABLE daily_ranking_entries_{$date_formatted}
            ADD CONSTRAINT fk_daily_ranking_entries_{$date_formatted}_daily_ranking_id FOREIGN KEY (daily_ranking_id)
                REFERENCES daily_rankings (daily_ranking_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT fk_daily_ranking_entries_{$date_formatted}_steam_user_id FOREIGN KEY (steam_user_id)
                REFERENCES steam_users (steam_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE;
        ");
    }

    public static function dropPartitionTableIndexes(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        db()->exec("
            DROP INDEX IF EXISTS idx_daily_ranking_entries_{$date_formatted}_steam_user_id;
        ");
    }
    
    public static function createPartitionTableIndexes(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        db()->exec("
            CREATE INDEX IF NOT EXISTS idx_daily_ranking_entries_{$date_formatted}_steam_user_id
            ON daily_ranking_entries_{$date_formatted}
            USING btree (steam_user_id);
        ");
    }
    

    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
        
        static::dropPartitionTableIndexes($date);
    
        db()->exec("
            CREATE TABLE daily_ranking_entries_{$date_formatted} (
                daily_ranking_id integer NOT NULL,
                steam_user_id integer NOT NULL,
                first_place_ranks smallint NOT NULL,
                top_5_ranks smallint NOT NULL,
                top_10_ranks smallint NOT NULL,
                top_20_ranks smallint NOT NULL,
                top_50_ranks smallint NOT NULL,
                top_100_ranks smallint NOT NULL,
                total_points double precision NOT NULL,
                total_dailies smallint NOT NULL,
                total_wins smallint NOT NULL,
                sum_of_ranks integer NOT NULL,
                total_score integer NOT NULL,
                rank integer NOT NULL,
                CONSTRAINT pk_daily_ranking_entries_{$date_formatted}_daily_ranking_entry_id PRIMARY KEY (daily_ranking_id, steam_user_id)
            )
            WITH (
                OIDS=FALSE
            );
        ");
        
        static::createPartitionTableConstraints($date);
        static::createPartitionTableIndexes($date);
    }
    
    public static function clear($daily_ranking_id, DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->delete("daily_ranking_entries_{$date_formatted}", array(
            'daily_ranking_id' => $daily_ranking_id
        ));
    }
    
    public static function getInsertQueue(DateTime $date) {
        return new InsertQueue("daily_ranking_entries_{$date->format('Y_m')}", db(), 4679);
    }
    
    public static function vacuum(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("VACUUM ANALYZE daily_ranking_entries_{$date_formatted};");
    }
    
    public static function getTempInsertQueue() {
        return new InsertQueue("daily_ranking_entries", db(), 600);
    }
    
    public static function createTemporaryTable() {
        db()->exec("
            CREATE TEMPORARY TABLE daily_ranking_entries
            (
                daily_ranking_id integer NOT NULL,
                steam_user_id integer NOT NULL,
                first_place_ranks smallint NOT NULL,
                top_5_ranks smallint NOT NULL,
                top_10_ranks smallint NOT NULL,
                top_20_ranks smallint NOT NULL,
                top_50_ranks smallint NOT NULL,
                top_100_ranks smallint NOT NULL,
                total_points double precision NOT NULL,
                total_dailies smallint NOT NULL,
                total_wins smallint NOT NULL,
                sum_of_ranks integer NOT NULL,
                total_score integer NOT NULL,
                rank integer NOT NULL
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveTempEntries(DateTime $date) {
        db()->exec("
            INSERT INTO daily_ranking_entries_{$date->format('Y_m')}
            SELECT *
            FROM daily_ranking_entries
        ");
    }    

    public static function getAllBaseResultset(DateTime $date, $release_id, $mode_id, $daily_ranking_day_type_id, $external_site_id) {            
        $date_formatted = $date->format('Y-m-d');
    
        $sql_resultset = new SQL('daily_ranking_entries');
        
        $sql_resultset->setBaseQuery("
            {{SELECT_FIELDS}}
            FROM daily_rankings dr
            JOIN daily_ranking_entries_{$date->format('Y_m')} dre ON dre.daily_ranking_id = dr.daily_ranking_id
            JOIN steam_users su ON su.steam_user_id = dre.steam_user_id
            {{JOIN_CRITERIA}}
            {{WHERE_CRITERIA}}
            {{ORDER_CRITERIA}}
        ");
        
        //These two calls need to be made in this order for optimal query speed
        ExternalSites::addSiteUserLeftJoins($sql_resultset);
        DatabaseEntry::setSelectFields($sql_resultset);
        
        $sql_resultset->addFilterCriteria("dr.date = ?", array(
            $date_formatted
        ));
        
        $sql_resultset->addFilterCriteria('dr.release_id = ?', array(
            $release_id
        ));
        
        $sql_resultset->addFilterCriteria('dr.mode_id = ?', array(
            $mode_id
        ));
        
        $sql_resultset->addFilterCriteria('dr.daily_ranking_day_type_id = ?', array(
            $daily_ranking_day_type_id
        ));
        
        $sql_resultset->setSortCriteria('dre.rank', 'ASC');
        
        $resultset = new HyrbidResultset('daily_ranking_entries', cache('database'), cache('local'));
        
        $resultset->setSqlResultset($sql_resultset, 'dre.steam_user_id');
        
        $resultset->setIndexName(CacheNames::getEntriesIndexName($release_id, $mode_id, $daily_ranking_day_type_id, array(
            $external_site_id
        )));
        
        $resultset->setPartitionName($date_formatted);
        
        return $resultset;
    }
    
    public static function getSteamUserBaseResultset($release_id, $mode_id, $steamid, DateTime $start_date, DateTime $end_date, $daily_ranking_day_type_id) {    
        $resultset = new SQL('steam_user_daily_ranking_entries');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'dr.date',
                'alias' => 'date'
            ),
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            )
        ));
        
        DatabaseEntry::setSelectFields($resultset);
        
        $resultset->setFromTable('daily_rankings dr');

        $resultset->addJoinCriteria("{{PARTITION_TABLE}} dre ON dre.daily_ranking_id = dr.daily_ranking_id");
        $resultset->addJoinCriteria("steam_users su ON su.steam_user_id = dre.steam_user_id");
        
        $parition_table_names = static::getPartitionTableNames('daily_ranking_entries', $start_date, $end_date);
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $resultset->addFilterCriteria('dr.date BETWEEN ? AND ?', array(
            $start_date->format('Y-m-d'),
            $end_date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('dr.release_id = ?', array(
            $release_id
        ));
        
        $resultset->addFilterCriteria('dr.mode_id = ?', array(
            $mode_id
        ));
        
        $resultset->addFilterCriteria('dr.daily_ranking_day_type_id = ?', array(
            $daily_ranking_day_type_id
        ));
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->setSortCriteria('date', 'DESC');
        
        $resultset->setCountResultset(clone $resultset);
        
        return $resultset;
    }
    
    public static function loadIntoCache(DateTime $date) {    
        $date_formatted = $date->format('Y-m-d');
    
        $resultset = new SQL("daily_ranking_entries_cache");
        
        $resultset->setBaseQuery("
            {{SELECT_FIELDS}}
            FROM daily_rankings dr 
            JOIN daily_ranking_entries_{$date->format('Y_m')} dre ON dre.daily_ranking_id = dr.daily_ranking_id
            JOIN steam_users su ON su.steam_user_id = dre.steam_user_id
            {{WHERE_CRITERIA}}
            ORDER BY dr.daily_ranking_id ASC
        ");
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'dr.release_id',
                'alias' => 'release_id'
            ),
            array(
                'field' => 'dr.mode_id',
                'alias' => 'mode_id'
            ),
            array(
                'field' => 'dr.daily_ranking_day_type_id',
                'alias' => 'daily_ranking_day_type_id'
            ),
            array(
                'field' => 'dre.steam_user_id',
                'alias' => 'steam_user_id'
            ),
            array(
                'field' => 'dre.rank',
                'alias' => 'rank'
            )
        ));
        
        ExternalSites::addSiteIdSelectFields($resultset);
        
        $resultset->addFilterCriteria("dr.date = :date", array(
            ':date' => $date_formatted
        ));

        $resultset->setAsCursor(30000);
        
        db()->beginTransaction();
        
        $transaction = cache('database')->transaction();
        
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
                    $daily_ranking_day_type_id = (int)$entry['daily_ranking_day_type_id'];
                    
                    $rank = (int)$entry['rank']; 
                    
                    ExternalSites::addToSiteIdIndexes(
                        $indexes, 
                        $entry, 
                        CacheNames::getEntriesIndexName($release_id, $mode_id, $daily_ranking_day_type_id, array()), 
                        $steam_user_id,
                        $rank
                    );
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
}