<?php
namespace Modules\Necrolab\Models\Modes\Database;

use \DateTime;
use \Framework\Data\ResultSet\SQL;
use \Modules\Necrolab\Models\Modes\Modes as BaseModes;

class Modes
extends BaseModes {
    protected static function loadAll() {        
        if(empty(static::$modes)) {        
            $modes = db()->getAll("
                SELECT *
                FROM modes
                ORDER BY sort_order ASC
            ");
            
            if(!empty($modes)) {
                static::$modes = $modes;
            }
        }
    }
    
    public static function getBaseResultset() {    
        $resultset = new SQL("modes");
        
        static::setSelectFields($resultset);
        
        $resultset->setFromTable('modes mo');
        
        $resultset->addSortCriteria('sort_order', 'ASC');
        
        return $resultset;
    }
    
    public static function setSelectFields($resultset) {
        $resultset->addSelectFields(array(
            array(
                'field' => 'mo.name',
                'alias' => 'mode_name'
            ),
            array(
                'field' => 'mo.display_name',
                'alias' => 'mode_display_name',
            )
        ));
    }
}