<?php
namespace Modules\Necrolab\Models\Dailies\Rankings\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Dailies\Rankings\Entries as BaseEntries;

class Entries
extends BaseEntries {    
    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("
            CREATE TABLE daily_ranking_entries_{$date_formatted} (
                daily_ranking_id smallint NOT NULL,
                steam_user_id integer NOT NULL,
                first_place_ranks smallint NOT NULL,
                top_5_ranks smallint NOT NULL,
                top_10_ranks smallint NOT NULL,
                top_20_ranks smallint NOT NULL,
                top_50_ranks smallint NOT NULL,
                top_100_ranks smallint NOT NULL,
                total_points double precision NOT NULL,
                points_per_day double precision NOT NULL,
                total_dailies smallint NOT NULL,
                total_wins smallint NOT NULL,
                average_rank double precision NOT NULL,
                sum_of_ranks integer NOT NULL,
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
            ON daily_ranking_entries
            USING btree
            (daily_ranking_id);

            CREATE INDEX idx_daily_ranking_entries_{$date_formatted}_rank_asc
            ON daily_ranking_entries
            USING btree
            (rank);

            CREATE INDEX idx_daily_ranking_entries_{$date_formatted}_rank_desc
            ON daily_ranking_entries
            USING btree
            (rank DESC);

            CREATE INDEX idx_daily_ranking_entries_{$date_formatted}_steam_user_id
            ON daily_ranking_entries
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

    protected static function getEntriesBaseResultset(DateTime $date, $number_of_days) {        
        $resultset = new SQL('daily_ranking_entries');
        
        $resultset->setBaseQuery("
            SELECT
                dre.rank,
                dre.first_place_ranks,
                dre.top_5_ranks,
                dre.top_10_ranks,
                dre.top_20_ranks,
                dre.top_50_ranks,
                dre.top_100_ranks,
                dre.total_points,
                dre.points_per_day,
                dre.total_dailies,
                dre.total_wins,
                dre.average_rank,
                dr.daily_ranking_id,
                dre.steam_user_id,
                drdt.daily_ranking_day_type_id,
                drdt.number_of_days,
                su.steamid
            FROM daily_rankings dr
            JOIN daily_ranking_entries_{$date->format('Y_m')} dre ON dre.daily_ranking_id = dr.daily_ranking_id
            JOIN steam_users su ON su.steam_user_id = dre.steam_user_id
            JOIN daily_ranking_day_types drdt ON drdt.daily_ranking_day_type_id = dr.daily_ranking_day_type_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria("dr.date = :date", array(
            ':date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('drdt.number_of_days = :number_of_days', array(
            ':number_of_days' => $number_of_days
        ));
    
        return $resultset;
    }

    public static function getEntriesResultset(DateTime $date, $number_of_days = NULL) {
        if(empty($number_of_days)) {
            $number_of_days = 0;
        }
    
        $resultset = static::getEntriesBaseResultset($date, $number_of_days);        
        
        $resultset->addSortCriteria('dre.rank', 'ASC');
    
        return $resultset;
    }
    
    protected static function getEntriesBaseDisplayResultset(DateTime $date, $number_of_days) {        
        $resultset = new SQL('daily_ranking_entries');
        
        $resultset->setBaseQuery("
            SELECT
                dre.rank,
                dre.first_place_ranks,
                dre.top_5_ranks,
                dre.top_10_ranks,
                dre.top_20_ranks,
                dre.top_50_ranks,
                dre.top_100_ranks,
                dre.total_points,
                dre.points_per_day,
                dre.total_dailies,
                dre.total_wins,
                dre.average_rank,
                dr.daily_ranking_id,
                dre.steam_user_id,
                drdt.daily_ranking_day_type_id,
                drdt.number_of_days,
                su.steamid,
                su.personaname,
                su.twitch_username,
                su.twitter_username,
                su.nico_nico_url,
                su.hitbox_username,
                su.website
            FROM daily_rankings dr
            JOIN daily_ranking_entries_{$date->format('Y_m')} dre ON dre.daily_ranking_id = dr.daily_ranking_id
            JOIN steam_users su ON su.steam_user_id = dre.steam_user_id
            JOIN daily_ranking_day_types drdt ON drdt.daily_ranking_day_type_id = dr.daily_ranking_day_type_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria("dr.date = :date", array(
            ':date' => $date->format('Y-m-d')
        ));
        
        $resultset->addFilterCriteria('drdt.number_of_days = :number_of_days', array(
            ':number_of_days' => $number_of_days
        ));
    
        return $resultset;
    }
    
    public static function getEntriesDisplayResultset(DateTime $date, $number_of_days = NULL) {
        if(empty($number_of_days)) {
            $number_of_days = 0;
        }
    
        $resultset = static::getEntriesBaseDisplayResultset($date, $number_of_days);  
        
        $resultset->setRowsPerPage(100);
        
        $resultset->addSortCriteria('dre.rank', 'ASC');
    
        return $resultset;
    }
}