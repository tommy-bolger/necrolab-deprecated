<?php
namespace Modules\Necrolab\Models\Characters\Database;

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
}