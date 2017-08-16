<?php
namespace Modules\Necrolab\Models\SteamUsers\Database;

use \DateTime;
use \Framework\Data\Database\InsertQueue;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Achievements\Achievements as AllAchievements;
use \Modules\Necrolab\Models\SteamUsers\Achievements as BaseAchievements;
use \Modules\Necrolab\Models\SteamUsers\Database\RecordModels\SteamUserAchievement as SteamUserAchievementRecord;

class Achievements
extends BaseAchievements {
    protected static function loadUser($steam_user_id) {        
        if(empty(static::$achievements_by_user[$steam_user_id])) {        
            $user_achievements = db()->getAll("
                SELECT *
                FROM steam_user_achievements
                WHERE steam_user_id = :steam_user_id
            ", array(
                ':steam_user_id' => $steam_user_id
            ));
            
            if(empty($user_achievements)) {
                $user_achievements = array();
            }
            
            static::$achievements_by_user[$steam_user_id] = $user_achievements;
        }
    }
    
    public static function loadIds() {
        if(empty(static::$ids)) {
            $ids = db()->getAll("
                SELECT 
                    steam_user_id,
                    achievement_id
                FROM steam_user_achievements
            ");
            
            if(!empty($ids)) {
                foreach($ids as $id_row) {
                    static::$ids[$id_row['steam_user_id']][$id_row['achievement_id']] = 1;
                }
            }
        }
    }
    
    public static function getBySteamid($steamid) {        
        return db()->getAll("
            SELECT sua.*
            FROM steam_users su
            JOIN steam_user_achievements sua ON sua.steam_user_id = su.steam_user_id
            WHERE su.steamid = :steamid
        ", array(
            ':steamid' => $steamid
        ));
    }
    
    public static function dropTableConstraints() {    
        db()->exec("
            ALTER TABLE steam_user_achievements
            DROP CONSTRAINT fk_sua_achievement_id,
            DROP CONSTRAINT fk_sua_steam_user_id;
        ");
    }
    
    public static function createTableConstraints() {        
        db()->exec("
            ALTER TABLE steam_user_achievements
            ADD CONSTRAINT fk_sua_achievement_id FOREIGN KEY (achievement_id)
                REFERENCES achievements (achievement_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE,
            ADD CONSTRAINT fk_sua_steam_user_id FOREIGN KEY (steam_user_id)
                REFERENCES steam_users (steam_user_id) MATCH SIMPLE
                ON UPDATE CASCADE ON DELETE CASCADE;
        ");
    }

    public static function dropTableIndexes() {
        db()->exec("
            DROP INDEX IF EXISTS idx_sua_achievement_id;
            DROP INDEX IF EXISTS idx_sua_steam_user_id;
        ");
    }
    
    public static function createTableIndexes() {
        db()->exec("
            CREATE INDEX idx_sua_achievement_id
            ON steam_user_achievements
            USING btree (achievement_id);           
            
            CREATE INDEX idx_sua_steam_user_id
            ON steam_user_achievements
            USING btree (steam_user_id);
        ");
    }    
    
    public static function save(SteamUserAchievementRecord $steam_user_achievement, $cache_query_name = NULL) {
        $steam_user_achievement_record = $steam_user_achievement->toArray();
        
        db()->insert('steam_user_achievements', $steam_user_achievement_record, $cache_query_name, false);
        
        static::$ids[$steam_user_achievement->steam_user_id][$steam_user_achievement->achievement_id] = 1;
    }
    
    public static function getInsertQueue() {
        return new InsertQueue("steam_user_achievements", db(), 20000);
    }
    
    public static function vacuum() {
        db()->exec("VACUUM ANALYZE steam_user_achievements;");
    }
    
    public static function getTempInsertQueue() {
        return new InsertQueue("steam_user_achievements_temp", db(), 20000);
    }
    
    public static function createTemporaryTable() {
        db()->exec("
            CREATE TEMPORARY TABLE steam_user_achievements_temp
            (
                steam_user_id integer NOT NULL,
                achievement_id smallint NOT NULL,
                achieved timestamp without time zone NOT NULL
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveTemp() {
        db()->query("
            INSERT INTO steam_user_achievements
            SELECT *
            FROM steam_user_achievements_temp
            ON CONFLICT (steam_user_id, achievement_id) DO NOTHING
        ");
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'sua.achieved',
                'alias' => 'achieved',
            )
        ));
    }
    
    public static function getAllBaseResultset() {    
        $resultset = new SQL("all_steam_user_achievements");
        
        $resultset->setBaseQuery("
            SELECT *
            FROM steam_user_achievements
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSortCriteria('steam_user_id', 'ASC');
        $resultset->addSortCriteria('achievement_id', 'ASC');
        
        return $resultset;
    }
    
    public static function getApiSteamUserResultset($steamid) {    
        $resultset = new SQL("steam_user_achievements");
        
        AllAchievements::setSelectFields($resultset);
        static::setSelectFields($resultset);
        
        $resultset->setFromTable('achievements a');
        
        $resultset->addLeftJoinCriteria("
            (
                SELECT 
                    sua.achievement_id,
                    sua.achieved
                FROM steam_users su
                JOIN steam_user_achievements sua ON sua.steam_user_id = su.steam_user_id
                WHERE su.steamid = :steamid
            ) sua ON sua.achievement_id = a.achievement_id
        ", array(
            ':steamid' => $steamid
        ));
        
        $resultset->addSortCriteria('a.achievement_id', 'ASC');
        
        return $resultset;
    }
}