<?php
namespace Modules\Necrolab\Models\Leaderboards\Database;

use \DateTime;
use \Exception;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Leaderboards\Leaderboards as BaseLeaderboards;
use \Modules\Necrolab\Models\Leaderboards\Database\RecordModels\Leaderboard as DatabaseLeaderboard;

class Leaderboards
extends BaseLeaderboards {
    public static function loadAll() {
        if(empty(static::$leaderboards)) {        
            $leaderboards = db()->getGroupedRows("
                SELECT 
                    lbid,
                    c.name as character_name,
                    l.*
                FROM leaderboards l
                JOIN characters c ON c.character_id = l.character_id
            ");
            
            if(!empty($leaderboards)) {
                static::$leaderboards = $leaderboards;
            }
        }
    }
    
    public static function getLeaderboardsBaseResultset() {
        $resultset = new SQL('leaderboards');
        
        $resultset->setBaseQuery("
            SELECT
                l.lbid,
                l.name,
                c.name AS character_name,
                l.is_speedrun,
                l.is_custom,
                l.is_co_op,
                l.is_seeded,
                l.is_daily,
                l.daily_date,
                l.is_score_run,
                l.is_all_character,
                l.is_deathless,
                l.is_story_mode,
                l.is_dev,
                l.is_prod,
                l.is_power_ranking,
                l.is_daily_ranking
            FROM leaderboards l
            JOIN characters c ON c.character_id = l.character_id
            LEFT JOIN leaderboards_blacklist lb ON lb.leaderboard_id = l.leaderboard_id
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addFilterCriteria('lb.leaderboards_blacklist_id IS NULL');
        
        return $resultset;
    }

    public static function getAllByCategory($category_name) {               
        $leaderboard_flag = '';

        switch($category_name) {
            case 'score':
                $leaderboard_flag = 'is_score_run';
                break;
            case 'speed':
                $leaderboard_flag = 'is_speedrun';
                break;
            case 'deathless':
                $leaderboard_flag = 'is_deathless';
                break;
            default:
                throw new Exception("Specified category name '{$category_name}' does not exist. It must be 'score', 'speed', or 'deathless'.");
                break;
        }
        
        $resultset = static::getLeaderboardsBaseResultset();
        
        $resultset->addFilterCriteria("{$leaderboard_flag} = 1");
        $resultset->addFilterCriteria("is_daily = 0");
        $resultset->addFilterCriteria("is_prod = 1");
    
        return $resultset->getAll();
    }
    
    public static function save(DatabaseLeaderboard $leaderboard_record) {
        $lbid = $leaderboard_record->lbid;
    
        $leaderboard = static::get($lbid);
        
        $leaderboard_fields = $leaderboard_record->toArray(false);
        
        $leaderboard_id = NULL;
        
        if(empty($leaderboard)) {
            $leaderboard_id = db()->insert('leaderboards', $leaderboard_fields, 'leaderboard_insert');
            
            $leaderboard_fields['leaderboard_id'] = $leaderboard_id;
            
            static::$leaderboards[$lbid] = $leaderboard_fields;
        }
        else {
            $leaderboard_id = $leaderboard['leaderboard_id'];
        
            db()->update('leaderboards', $leaderboard_fields, array(
                'leaderboard_id' => $leaderboard_id
            ), array(), 'leaderboard_update');
            
            static::$leaderboards[$lbid] = array_merge(static::$leaderboards[$lbid], $leaderboard_fields);
        }
        
        return $leaderboard_id;
    }
}