<?php
namespace Modules\Necrolab\Models\Rankings\Database;

use \DateTime;

class Entries {
    public static function createPartitionTable(DateTime $date) {
        $date_formatted = $date->format('Y_m');
    
        db()->exec("        
            CREATE TABLE power_ranking_entries_{$date_formatted}
            (
                power_ranking_id smallint NOT NULL,
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
                score_rank integer,
                score_rank_points_total double precision,
                deathless_rank integer,
                deathless_rank_points_total double precision,
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
}