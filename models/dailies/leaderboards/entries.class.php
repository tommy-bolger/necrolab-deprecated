<?php
namespace Modules\Necrolab\Models\Dailies\Leaderboards;

use \Modules\Necrolab\Models\Necrolab;

class Entries
extends Necrolab {
    protected $lbid;
    
    protected $entries = array();

    public function __construct($lbid) {
        parent::__construct();
    
        $this->lbid = $lbid;
    }

    public function add($leaderboard_entry) {
        $this->entries[] = $leaderboard_entry->toArray(false);
    }
    
    public function save(DateTime $date) {}
}