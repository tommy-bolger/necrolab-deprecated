<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Modes\Database\Modes as DatabaseModes;
use \Modules\Necrolab\Models\Dailies\Rankings\Database\Entry as DatabaseEntry;
use \Modules\Necrolab\Models\ExternalSites\Database\ExternalSites as DatabaseExternalSites;
use \Modules\Necrolab\Models\Dailies\Rankings\Entries as BaseEntries;

class Entries
extends BaseEntries {    
    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
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
            CONSTRAINT pk_daily_ranking_entries_{$date_formatted}_daily_ranking_entry_id PRIMARY KEY (daily_ranking_id, steam_user_id),
            CONSTRAINT fk_daily_ranking_entries_{$date_formatted}_daily_ranking_id FOREIGN KEY (daily_ranking_id)
                REFERENCES daily_rankings (daily_ranking_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            CONSTRAINT fk_daily_ranking_entries_{$date_formatted}_steam_user_id FOREIGN KEY (steam_user_id)
                REFERENCES steam_users (steam_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE
            )
            WITH (
                OIDS=FALSE
            );

            CREATE INDEX idx_daily_ranking_entries_{$date_formatted}_daily_ranking_id
            ON daily_ranking_entries_{$date_formatted}
            USING btree
            (daily_ranking_id);

            CREATE INDEX idx_daily_ranking_entries_{$date_formatted}_steam_user_id
            ON daily_ranking_entries_{$date_formatted}
            USING btree
            (steam_user_id);
        ");
    }
    
    public static function clear($daily_ranking_id, DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->delete("daily_ranking_entries_{$date_formatted}", array(
            'daily_ranking_id' => $daily_ranking_id
        ));
    }

    public static function getAllBaseResultset($release_name, $mode_name, DateTime $date, $number_of_days = NULL) {
        if(empty($number_of_days)) {
            $number_of_days = 0;
        }
    
        $resultset = new SQL('daily_ranking_entries');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'dr.date',
                'alias' => 'date'
            ),
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            ),
            array(
                'field' => 'su.personaname',
                'alias' => 'personaname'
            ),
            array(
                'field' => 'dr.daily_ranking_id',
                'alias' => 'daily_ranking_id'
            ),
            array(
                'field' => 'drdt.daily_ranking_day_type_id',
                'alias' => 'daily_ranking_day_type_id'
            ),
            array(
                'field' => 'drdt.number_of_days',
                'alias' => 'number_of_days'
            )
        ));
        
        DatabaseEntry::setSelectFields($resultset);
        
        $resultset->setFromTable('daily_rankings dr');
        
        $resultset->addJoinCriteria('daily_ranking_day_types drdt ON drdt.daily_ranking_day_type_id = dr.daily_ranking_day_type_id');
        $resultset->addJoinCriteria('releases r ON r.release_id = dr.release_id');
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = dr.mode_id');
        $resultset->addJoinCriteria("daily_ranking_entries_{$date->format('Y_m')} dre ON dre.daily_ranking_id = dr.daily_ranking_id");
        $resultset->addJoinCriteria("steam_users su ON su.steam_user_id = dre.steam_user_id");
        
        
        $resultset->addFilterCriteria("dr.date = :date", array(
            ':date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('r.name = :release_name', array(
            ':release_name' => $release_name
        ));
        
        $resultset->addFilterCriteria('mo.name = :mode_name', array(
            ':mode_name' => $mode_name
        ));
        
        $resultset->addFilterCriteria('drdt.number_of_days = :number_of_days', array(
            ':number_of_days' => $number_of_days
        ));
        
        $resultset->addSortCriteria('dre.rank', 'ASC');
        
        $resultset->setRowsPerPage(100);
        
        DatabaseExternalSites::addSiteUserLeftJoins($resultset);
    
        return $resultset;
    }
    
    public static function getSteamUserBaseResultset($release_name, $steamid, DateTime $start_date, DateTime $end_date, $number_of_days = NULL) {
        if(empty($number_of_days)) {
            $number_of_days = 0;
        }
    
        $resultset = new SQL('steam_user_daily_ranking_entries');
        
        $resultset->addSelectFields(array(
            array(
                'field' => 'dr.date',
                'alias' => 'date'
            ),
            array(
                'field' => 'su.steamid',
                'alias' => 'steamid'
            ),
            array(
                'field' => 'dr.daily_ranking_id',
                'alias' => 'daily_ranking_id'
            ),
            array(
                'field' => 'drdt.daily_ranking_day_type_id',
                'alias' => 'daily_ranking_day_type_id'
            ),
            array(
                'field' => 'drdt.number_of_days',
                'alias' => 'number_of_days'
            )
        ));
        
        DatabaseEntry::setSelectFields($resultset);
        DatabaseModes::setSelectFields($resultset);
        
        $resultset->setFromTable('daily_rankings dr');
        
        $resultset->addJoinCriteria('releases r ON r.release_id = dr.release_id');
        $resultset->addJoinCriteria('modes mo ON mo.mode_id = dr.mode_id');
        $resultset->addJoinCriteria('daily_ranking_day_types drdt ON drdt.daily_ranking_day_type_id = dr.daily_ranking_day_type_id');
        $resultset->addJoinCriteria("{{PARTITION_TABLE}} dre ON dre.daily_ranking_id = dr.daily_ranking_id");
        $resultset->addJoinCriteria("steam_users su ON su.steam_user_id = dre.steam_user_id");
        
        $parition_table_names = static::getPartitionTableNames('daily_ranking_entries', $start_date, $end_date);
        
        foreach($parition_table_names as $parition_table_name) {
            $resultset->addPartitionTable($parition_table_name);
        }
        
        $resultset->addFilterCriteria('su.steamid = ?', array(
            $steamid
        ));
        
        $resultset->addFilterCriteria('dr.date BETWEEN ? AND ?', array(
            $start_date->format('Y-m-d'),
            $end_date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('r.name = ?', array(
            $release_name
        ));
        
        $resultset->addFilterCriteria('drdt.number_of_days = ?', array(
            $number_of_days
        ));
        
        $resultset->setSortCriteria('date', 'ASC');
        
        return $resultset;
    }
}