<?php
namespace Modules\Necrolab\Models\Characters\Cache;

use \Modules\Necrolab\Models\Characters\Characters as BaseCharacters;
use \Modules\Necrolab\Models\Characters\Database\Characters as DatabaseCharacters;
use \Modules\Necrolab\Models\Characters\Cache\RecordModels\Character;

class Characters
extends BaseCharacters {    
    protected static function loadAll() {
        if(empty(static::$characters)) {
            $character_entries = cache('read')->hGetAll(CacheNames::getEntriesName());
            
            if(!empty($character_entries)) {
                $characters = cache('read')->hGetAllMulti($character_entries);
            
                foreach($characters as $character) {                
                    static::$characters[$character['name']] = $character;
                }
            }
        }
    }
    
    public static function loadIntoCache() {
        $characters = DatabaseCharacters::getAll();
        
        $transaction = cache('write')->transaction();
        
        $transaction->delete(CacheNames::CHARACTERS);
        $transaction->delete(CacheNames::getIdsByName());
        $transaction->delete(CacheNames::getEntriesName());
        
        foreach($characters as $character) {
            $character_model = new Character();
            $character_model->setPropertiesFromArray($character);
        
            $hash_name = CacheNames::getEntryName($character_model->name);
            
            $transaction->delete($hash_name);
                        
            $transaction->hMset($hash_name, $character_model->toArray());
            
            $transaction->hSet(CacheNames::CHARACTERS, $character_model->name, $character['character_id']);
            $transaction->hSet(CacheNames::getEntriesName(), $character_model->name, $hash_name);
        }
        
        $transaction->commit();
    }
}