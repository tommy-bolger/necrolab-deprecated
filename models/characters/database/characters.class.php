<?php
namespace Modules\Necrolab\Models\Characters\Database;

use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Characters\Characters as BaseCharacters;
use \Modules\Necrolab\Models\Characters\Database\RecordModels\Character;

class Characters
extends BaseCharacters {
    protected static function loadAll() {
        if(empty(static::$characters)) {
            $characters = db()->getAll("
                SELECT *
                FROM characters
            ");
            
            if(!empty($characters)) {
                foreach($characters as $character) {                
                    static::$characters[$character['name']] = $character;
                }
            }
        }
    }
    
    public static function getAllBaseResultset() {    
        $resultset = new SQL("characters");
        
        $resultset->setBaseQuery("
            SELECT *
            FROM characters
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->addSortCriteria('sort_order', 'ASC');
        
        return $resultset;
    }
    
    public static function getAllActiveBaseResultset() {    
        $resultset = static::getAllBaseResultset();
        
        $resultset->addFilterCriteria('is_active = 1');
        
        return $resultset;
    }
}