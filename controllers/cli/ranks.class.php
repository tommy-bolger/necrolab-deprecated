<?php
namespace Modules\Necrolab\Controllers\Cli;

use \Framework\Core\Controllers\Cli;
use \Modules\Necrolab\Models\Leaderboards\Database\Ranks as DatabaseRanks;

class Ranks
extends Cli {        
    public function actionGenerate($limit = 1000000) {
        DatabaseRanks::populateTable($limit);
        
        DatabaseRanks::vacuum();
    }
}