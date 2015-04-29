<?php
namespace Modules\Necrolab\Models;

use \SimpleXMLElement;
use \DateTime;
use \Framework\Modules\Module;
use \Framework\Data\XMLWrite;

class LeaderboardImport {
    protected $framework;

    protected $module;

    protected $stored_leaderboards = array();
    
    protected $steam_users = array();
    
    protected $characters = array();
    
    protected $caching_mode = false;
    
    protected $verbose_output = false;
    
    protected $cache;
    
    protected $leaderboards_url;
    
    protected $imported_leaderboards = array();
    
    protected $imported_leaderboard_max_ranks = array();
    
    protected $new_steam_users = array();
    
    protected $saved_steam_users = array();
    
    public function __construct($cli_framework, $caching_mode, $verbose_output) {    
        assert('is_bool($caching_mode)');
        assert('is_bool($verbose_output)');
        
        $this->framework = $cli_framework;
        
        $this->caching_mode = $caching_mode;
        $this->verbose_output = $verbose_output;
        
        if($caching_mode) {
            $this->cache = cache();
        }
        
        $this->module = new Module('necrolab');
        
        $this->leaderboards_url = $this->module->configuration->leaderboard_url;
    }
    
    public function run() {
        $this->loadUserCache();
        $this->loadCharacterCache();
        
        $this->importLeaderboards();
        
        if(!$this->caching_mode) {
            if($this->verbose_output) {
                $this->framework->coutLine("Saving leaderboards to the database.");
            }
        
            $this->saveImportedLeaderboardsToDatabase();
        }
        else {
            if($this->verbose_output) {
                $this->framework->coutLine("Saving leaderboards to redis.");
            }
            
            $this->saveImportedLeaderboardsToCache();
        }
        
        $this->importLeaderboardEntries();
        
        if(!$this->caching_mode) {
            if($this->verbose_output) {
                $this->framework->coutLine("Saving new users to the database.");
            }
        
            $this->saveNewUsersToDatabase();
            
            if($this->verbose_output) {
                $this->framework->coutLine("Saving leaderboard entries to the database.");
            }
        
            $this->saveLeaderboardEntriesToDatabase();
        }
        else {
            if($this->verbose_output) {
                $this->framework->coutLine("Saving new users to redis.");
            }
            
            $this->saveNewUsersToCache();
        
            if($this->verbose_output) {
                $this->framework->coutLine("Saving leaderboard entries to redis.");
            }
            
            $this->saveLeaderboardEntriesToCache();
        }
    }
    
    protected function loadUserCache() {
        if($this->verbose_output) {
            $this->framework->coutLine("Loading user cache.");
        }
        
        if($this->caching_mode) {
            if($this->verbose_output) {
                $this->framework->coutLine("Loading stored users from redis.");
            }
        
            $this->steam_users = $this->cache->lRange('stored_steam_ids', 0, -1);
            
            $this->steam_users = array_combine($this->steam_users, $this->steam_users);
        }
        else {
            if($this->verbose_output) {
                $this->framework->coutLine("Loading stored users from database.");
            }
        
            $this->steam_users = db()->getMappedColumn("
                SELECT
                    steamid,
                    steam_user_id
                FROM steam_users
            "); 
        }
    }
    
    protected function loadCharacterCache() {    
        if($this->verbose_output) {
            $this->framework->coutLine("Loading character cache.");
        }
        
        if($this->caching_mode) {
            if($this->verbose_output) {
                $this->framework->coutLine("Attempting to load characters from redis.");
            }
        
            $this->characters = $this->cache->zRange('characters_by_name', 0, -1, true);
        }
        
        $characters_retrieved_from_database = false;
        
        if(empty($this->characters)) {
            if($this->verbose_output) {
                $this->framework->coutLine("Loading characters from database.");
            }
            
            $characters_retrieved_from_database = true;
        
            $this->characters = db()->getMappedColumn("
                SELECT
                    name,
                    character_id
                FROM characters
            ");
        }
        
        if($this->caching_mode && $characters_retrieved_from_database && !empty($this->characters)) {
            if($this->verbose_output) {
                $this->framework->coutLine("Saving database characters into redis.");
            }
            
            $transaction = $this->cache->multi();
            
            foreach($this->characters as $name => $character_id) {
                $transaction->zAdd('characters_by_name', $character_id, $name);
            }
            
            $transaction->exec();
        }
    }
    
    public function importLeaderboards() {
        if($this->verbose_output) {
            $this->framework->coutLine("Retrieving master leaderboards XML from Steam.");
        }
    
        $leaderboards_xml = file_get_contents($this->leaderboards_url);
        
        $leaderboards = XMLWrite::convertXmlToObject(new SimpleXMLElement($leaderboards_xml));
        
        unset($leaderboards_xml);
        
        if(!empty($leaderboards->leaderboard)) {
            $current_date_timestamp = strtotime(date('Y-m-d'));
        
            foreach($leaderboards->leaderboard as &$leaderboard) {
                if($this->verbose_output) {
                    $this->framework->coutLine("===== Working on leaderboard '{$leaderboard->name}'. =====");
                }
                
                $leaderboard_name = strtolower($leaderboard->name);
                
                $character_id = NULL;
                $character_name = '';
                
                /*
                    Retrieve which character this leaderboard is for.
                    This is done by looking for a case insensitive version of each charcter's name.
                    This is sloppy, but the only way I could find to fairly reliably get the character of each leaderboard with the data given.
                */
                if(strpos($leaderboard_name, 'bard') !== false) {
                    $character_id = $this->characters['bard'];
                    $character_name = 'bard';
                }
                elseif(strpos($leaderboard_name, 'aria') !== false) {
                    $character_id = $this->characters['aria'];
                    $character_name = 'aria';
                }
                elseif(strpos($leaderboard_name, 'monk') !== false) {
                    $character_id = $this->characters['monk'];
                    $character_name = 'monk';
                }
                elseif(strpos($leaderboard_name, 'bolt') !== false) {
                    $character_id = $this->characters['bolt'];
                    $character_name = 'bolt';
                }
                elseif(strpos($leaderboard_name, 'dove') !== false) {
                    $character_id = $this->characters['dove'];
                    $character_name = 'dove';
                }
                elseif(strpos($leaderboard_name, 'eli') !== false) {
                    $character_id = $this->characters['eli'];
                    $character_name = 'eli';
                }
                elseif(strpos($leaderboard_name, 'melody') !== false) {
                    $character_id = $this->characters['melody'];
                    $character_name = 'melody';
                }
                elseif(strpos($leaderboard_name, 'dorian') !== false) {
                    $character_id = $this->characters['dorian'];
                    $character_name = 'dorian';
                }
                elseif(strpos($leaderboard_name, 'coda') !== false) {
                    $character_id = $this->characters['coda'];
                    $character_name = 'coda';
                }
                elseif(strpos($leaderboard_name, 'ghost') !== false) {
                    $character_id = $this->characters['ghost'];
                    $character_name = 'ghost';
                }
                elseif(strpos($leaderboard_name, 'pacifist') !== false) {
                    $character_id = $this->characters['pacifist'];
                    $character_name = 'pacifist';
                }
                elseif(strpos($leaderboard_name, 'thief') !== false) {
                    $character_id = $this->characters['thief'];
                    $character_name = 'thief';
                }
                //If nobody else assume it's Cadence
                else {
                    $character_id = $this->characters['cadence'];
                    $character_name = 'cadence';
                }
                
                $is_speedrun = 0;
                $is_custom = 0;
                $is_co_op = 0;
                $is_seeded = 0;
                $is_daily = 0;
                $daily_date = NULL;
                $is_score_run = 0;
                $is_all_character = 0;
                $is_deathless = 0;
                $is_story_mode = 0;
                $is_dev = 0;
                $is_prod = 0;
                
                if(strpos($leaderboard_name, 'speedrun') !== false) {
                    $is_speedrun = 1;
                    $is_score_run = 0;
                }
                
                if(strpos($leaderboard_name, 'custom') !== false) {
                    $is_custom = 1;
                }
                
                if(strpos($leaderboard_name, 'co-op') !== false) {
                    $is_co_op = 1;
                }
                
                if(strpos($leaderboard_name, 'seeded') !== false) {
                    $is_seeded = 1;
                }
                
                if(strpos($leaderboard_name, 'hardcore') !== false || strpos($leaderboard_name, 'all zones') !== false) {
                    $is_speedrun = 0;
                    $is_score_run = 1;
                }
                
                if(strpos($leaderboard_name, 'all chars') !== false) {
                    $is_all_character = 1;
                    $character_id = $this->characters['all'];
                    $character_name = 'all';
                }
                
                if(strpos($leaderboard_name, 'deathless') !== false) {
                    $is_deathless = 1;
                }
                
                if(strpos($leaderboard_name, 'story') !== false) {
                    $is_story_mode = 1;
                }
                
                if(strpos($leaderboard_name, 'dev') !== false) {
                    $is_dev = 1;
                }
                
                if(strpos($leaderboard_name, 'prod') !== false) {
                    $is_prod = 1;
                }
                
                /*
                    If this run is a daily then grab the date it is for.
                    Date matching solution found at: http://stackoverflow.com/a/7645146
                    Date filtering solution found at: http://stackoverflow.com/a/4639488  
                */
                $unformatted_daily_date = preg_replace("/[^0-9\/]/", "", $leaderboard_name);
                $daily_date_timestamp = NULL;
                
                if(!empty($unformatted_daily_date)) {
                    $is_daily = 1;
                    $is_speedrun = 0;
                    $is_score_run = 1;
    
                    $daily_date_object = DateTime::createFromFormat('d/m/Y', $unformatted_daily_date);
                    
                    $daily_date = $daily_date_object->format('Y-m-d');
                    
                    $daily_date_timestamp = strtotime($daily_date);
                }
                
                if(!empty($is_prod) && (empty($daily_date_timestamp) || $daily_date_timestamp >= $current_date_timestamp)) {
                    if($this->verbose_output) {
                        $this->framework->coutLine("Adding leaderboard to the process queue.");
                    }
                
                    $this->imported_leaderboards[$leaderboard->lbid] = array(
                        'name' => $leaderboard->name,
                        'url' => $leaderboard->url,
                        'lbid' => $leaderboard->lbid,
                        'display_name' => $leaderboard->display_name,
                        'entries' => $leaderboard->entries,
                        'sortmethod' => $leaderboard->sortmethod,
                        'displaytype' => $leaderboard->displaytype,
                        'onlytrustedwrites' => $leaderboard->onlytrustedwrites,
                        'onlyfriendsreads' => $leaderboard->onlyfriendsreads,
                        'character_id' => $character_id,
                        'character_name' => $character_name,
                        'is_speedrun' => $is_speedrun,
                        'is_custom' => $is_custom,
                        'is_co_op' => $is_co_op,
                        'is_seeded' => $is_seeded,
                        'is_daily' => $is_daily,
                        'daily_date' => $daily_date,
                        'is_score_run' => $is_score_run,
                        'is_all_character' => $is_all_character,
                        'is_deathless' => $is_deathless,
                        'is_story_mode' => $is_story_mode,
                        'is_dev' => $is_dev,
                        'is_prod' => $is_prod
                    );   
                }
                else {
                    if($this->verbose_output) {
                        $this->framework->coutLine("Leaderboard does not need to be processed. Skipping.");
                    }
                }
            }
        }
    }
    
    protected function saveImportedLeaderboardsToDatabase() {  
        if(!empty($this->imported_leaderboards)) {
            if($this->verbose_output) {
                $this->framework->coutLine("===== Saving imported leaderboards to the database. =====");
            }
        
            $stored_leaderboards = db()->getMappedColumn("
                SELECT
                    lbid,
                    leaderboard_id
                FROM leaderboards
            ");
        
            foreach($this->imported_leaderboards as &$imported_leaderboard) {
                $lbid = $imported_leaderboard['lbid'];
                $leaderboard_id = NULL;
            
                if(empty($stored_leaderboards[$lbid])) {
                    unset($imported_leaderboard['character_name']);
                    
                    if($this->verbose_output) {
                        $this->framework->coutLine("Saving leaderboard '{$imported_leaderboard['name']}' to the database.");
                    }
                    
                    $leaderboard_id = db()->insert('leaderboards', $imported_leaderboard, 'add_leaderboard');
                }
                else {
                    $leaderboard_id = $stored_leaderboards[$lbid];
                }
            
                $imported_leaderboard['leaderboard_id'] = $leaderboard_id;
            }
        }
    }
    
    public function saveImportedLeaderboardsToCache() {
        if(!empty($this->imported_leaderboards)) {     
            if($this->verbose_output) {
                $this->framework->coutLine("Saving imported leaderboards to redis.");
            }
                               
            $transaction = $this->cache->multi();

            foreach($this->imported_leaderboards as &$imported_leaderboard) {
                $leaderboard_id = md5(uniqid(mt_rand(), true));
            
                $imported_leaderboard['leaderboard_id'] = $leaderboard_id;
            
                $hash_name = "leaderboard:{$leaderboard_id}";
                
                if($this->verbose_output) {
                    $this->framework->coutLine("Saving leaderboard '{$imported_leaderboard['name']}' to redis.");
                }
                
                //Determine if the leaderboard is a power ranking leaderboard and add it to its own group
                $score_or_speed_run = false;
                
                if(!empty($imported_leaderboard['is_score_run']) || !empty($imported_leaderboard['is_speedrun'])) {
                    $score_or_speed_run = true;
                }
                
                if(
                    $score_or_speed_run && 
                    empty($imported_leaderboard['is_custom']) && 
                    empty($imported_leaderboard['is_co_op']) && 
                    empty($imported_leaderboard['is_seeded']) && 
                    empty($imported_leaderboard['is_daily']) &&
                    empty($imported_leaderboard['is_dev']) && 
                    empty($imported_leaderboard['is_prod'])
                ) {
                    $is_power_ranking = 1;
                
                    $imported_leaderboard['is_power_ranking'] = $is_power_ranking;

                    $transaction->rPush('power_ranking_leaderboards_new', $hash_name);
                }
                
                $transaction->hMset($hash_name, $imported_leaderboard);
                $transaction->rPush('leaderboards_new', $hash_name);
            }
            
            $transaction->rename('leaderboards', 'leaderboards_old');

            $transaction->rename('leaderboards_new', 'leaderboards');
            
            $transaction->rename('power_ranking_leaderboards', 'power_ranking_leaderboards_old');

            $transaction->rename('power_ranking_leaderboards_new', 'power_ranking_leaderboards');
            
            $transaction->rename('daily_ranking_leaderboards', 'daily_ranking_leaderboards_old');

            $transaction->rename('daily_ranking_leaderboards_new', 'daily_ranking_leaderboards');
            
            $transaction->exec();
            
            $old_leaderboard_keys = $this->cache->lRange('leaderboards_old', 0, -1);
            
            $transaction = $this->cache->multi();
            
            if(!empty($old_leaderboard_keys)) {            
                foreach($old_leaderboard_keys as &$old_leaderboard_key) {
                    $transaction->delete($old_leaderboard_key);
                }
                
                $transaction->delete('leaderboards_old');
            }
            
            $transaction->delete('power_ranking_leaderboards_old');
            $transaction->delete('daily_ranking_leaderboards_old');
            
            $transaction->exec();
        }
    }
    
    public function importLeaderboardEntries() {
        if(!empty($this->imported_leaderboards)) {
            foreach($this->imported_leaderboards as &$imported_leaderboard) {
                if($this->verbose_output) {
                    $this->framework->coutLine("Importing {$imported_leaderboard['entries']} entries for leaderboard '{$imported_leaderboard['name']}'.");
                }
                
                $lbid = $imported_leaderboard['lbid'];
                
                $next_page_url = $imported_leaderboard['url'];
                
                $max_rank = 1;
                
                do {
                    $leaderboard_users_xml = file_get_contents($next_page_url);
                    
                    if(!empty($leaderboard_users_xml)) {
                        $leaderboard_users = XMLWrite::convertXmlToObject(new SimpleXMLElement($leaderboard_users_xml));
        
                        unset($leaderboard_users_xml);
                                                
                        $this->importPageEntriesForLeaderboard($lbid, $imported_leaderboard, $leaderboard_users, $max_rank);
                    }
                    
                    if(!empty($leaderboard_users->nextRequestURL)) {                    
                        $next_page_url = trim($leaderboard_users->nextRequestURL);

                        if(!empty($next_page_url)) {
                            if($this->verbose_output) {
                                $this->framework->coutLine("Loading next page of users.");
                            }
                        }
                    }
                    else {
                        $next_page_url = NULL;
                    }
                }
                while(!empty($next_page_url));
                
                $this->imported_leaderboard_max_ranks[$lbid] = $max_rank;
            }
        }
    }
    
    protected function importPageEntriesForLeaderboard($lbid, $leaderboard_record, $leaderboard_users, &$max_rank) {
        $entries = array();

        if(!empty($leaderboard_users->entries->entry)) {
            if(is_array($leaderboard_users->entries->entry)) {
                $entries = $leaderboard_users->entries->entry;
            }
            else {
                $entries[] = $leaderboard_users->entries->entry;
            }
        }

        if(!empty($entries)) {            
            $entry_count = count($entries);
        
            if($this->verbose_output) {
                $this->framework->coutLine("{$entry_count} leaderboard entries found, processing entries.");
            }
            
            $leaderboard_id = $leaderboard_record['leaderboard_id'];
            $leaderboard_name = $leaderboard_record['name'];
            
            foreach($entries as &$leaderboard_user) {
                $steam_user_id = NULL;
            
                if(empty($this->steam_users[$leaderboard_user->steamid])) {
                    if($this->verbose_output) {
                        $this->framework->coutLine("Adding user {$leaderboard_user->steamid} to process queue.");
                    }
                    
                    $this->new_steam_users[$leaderboard_user->steamid] = array(
                        'steamid' => $leaderboard_user->steamid,
                    );
                    
                    $steam_user_id = $leaderboard_user->steamid;
                }
                else {
                    $steam_user_id = $this->steam_users[$leaderboard_user->steamid];
                }
                
                $score = $leaderboard_user->score;
                
                //Any scores from a score run greater than 300000 gold would be considered cheating and should be excluded from the rankings.
                if((!empty($leaderboard_record['is_score_run']) && $score <= 300000) || !empty($leaderboard_record['is_speedrun'])) {     
                    if($this->verbose_output) {
                        $this->framework->coutLine("Adding entry for leaderboard '{$leaderboard_name}'.");
                    }
                           
                    $time = NULL;
                
                    if(!empty($leaderboard_record['is_speedrun'])) {
                        $time = 100000000 - $score;
                    }
                    
                    $is_win = 0;
                    
                    if($leaderboard_user->details == '0300000005000000') {
                        $is_win = 1;
                    }

                    $success = apcu_store("leaderboard_entry_{$lbid}_{$max_rank}", array(
                        'leaderboard_id' => $leaderboard_id,
                        'steam_user_id' => $steam_user_id,
                        'score' => $score,
                        'rank' => $max_rank,
                        'ugcid' => $leaderboard_user->ugcid,
                        'details' => $leaderboard_user->details,
                        'time' => $time,
                        'is_win' => $is_win
                    ));
                    
                    $max_rank += 1;
                }
            }
        }
    }
    
    protected function saveNewUsersToDatabase() {
        if(!empty($this->new_steam_users)) {        
            foreach($this->new_steam_users as &$new_steam_user) {
                $steamid = $new_steam_user['steamid'];
            
                if($this->verbose_output) {
                    $this->framework->coutLine("Adding user {$steamid} to the database.");
                }
            
                $steam_user_id = db()->insert('steam_users', $new_steam_user, 'add_steam_user');
                
                $this->steam_users[$steamid] = $steam_user_id;
                $this->saved_steam_users[$steamid] = $steam_user_id;
            }
            
            $this->new_steam_users = array();
        }
    }
    
    protected function saveNewUsersToCache() {    
        if(!empty($this->new_steam_users)) {            
            $transaction = $this->cache->multi();
        
            foreach($this->new_steam_users as $steam_id => &$new_steam_user) {
                $steamid = $new_steam_user['steamid'];
                
                if($this->verbose_output) {
                    $this->framework->coutLine("Adding user {$steamid} to redis.");
                }
                            
                $hash_name = "steam_users:{$steamid}";
        
                $transaction->hMset($hash_name, $new_steam_user);
                $transaction->rPush('steam_users', $hash_name);
                $transaction->rPush('stored_steam_ids', $steamid);  
                
                $this->steam_users[$steamid] = $steamid;
                $this->saved_steam_users[$steamid] = $steamid;
            }
            
            $transaction->exec();
            
            $this->new_steam_users = array();
        }
    }
    
    protected function saveLeaderboardEntriesToDatabase() {
        if(!empty($this->imported_leaderboard_max_ranks)) {
            $current_date = date('Y-m-d');
        
            foreach($this->imported_leaderboard_max_ranks as $lbid => &$max_rank) {                
                $leaderboard_record = $this->imported_leaderboards[$lbid];
    
                $leaderboard_id = $leaderboard_record['leaderboard_id'];
                
                $leaderboard_snapshot_id = db()->getOne("
                    SELECT leaderboard_snapshot_id
                    FROM leaderboard_snapshots
                    WHERE leaderboard_id = ?
                        AND date = ?
                ", array(
                    $leaderboard_id,
                    $current_date
                ));
                
                if(empty($leaderboard_snapshot_id)) {
                    if($this->verbose_output) {
                        $this->framework->coutLine("No existing leaderboard snapshot was found for leaderboard '{$leaderboard_record['name']}' for today. Creating a new one.");
                    }
                
                    $leaderboard_snapshot_id = db()->insert('leaderboard_snapshots', array(
                        'leaderboard_id' => $leaderboard_id,
                        'date' => $current_date,
                        'created' => date('Y-m-d H:i:s')
                    ), 'add_leaderboard_snapshot');
                }
                else {
                    if($this->verbose_output) {
                        $this->framework->coutLine("An existing snapshot was found for for leaderboard '{$leaderboard_record['name']}' for today. Deleting existing data to replace with new records.");
                    }
                
                    db()->update('leaderboard_snapshots', array(
                        'updated' => date('Y-m-d H:i:s')
                    ), array(
                        'leaderboard_snapshot_id' => $leaderboard_snapshot_id
                    ), array(), 'update_leaderboard_snapshot');
                
                    db()->delete('leaderboard_entries', array(
                        'leaderboard_snapshot_id' => $leaderboard_snapshot_id
                    ), array(), 'delete_leaderboard_entries');
                    
                    if($this->verbose_output) {
                        $this->framework->coutLine("Updating leaderboard with latest snapshot.");
                    }
                }
                
                if($this->verbose_output) {
                    $this->framework->coutLine("Linking leaderboard with snapshot.");
                }

                db()->update('leaderboards', array(
                    'last_snapshot_id' => $leaderboard_snapshot_id
                ), array(
                    'leaderboard_id' => $leaderboard_id
                ), array(), 'update_leaderboard_latest_snapshot');
    
                $this->imported_leaderboards[$lbid]['last_snapshot_id'] = $leaderboard_snapshot_id;
                
                if($this->verbose_output) {
                    $this->framework->coutLine("Saving snapshot entries for leaderboard '{$leaderboard_record['name']}'.");
                }
            
                for($current_rank = 1; $current_rank <= $max_rank; $current_rank++) {
                    $leaderboard_entry = apcu_fetch("leaderboard_entry_{$lbid}_{$current_rank}");

                    if(!empty($leaderboard_entry)) {
                        $leaderboard_entry['leaderboard_snapshot_id'] = $leaderboard_snapshot_id;
        
                        if(!empty($this->saved_steam_users[$leaderboard_entry['steam_user_id']])) {
                            $leaderboard_entry['steam_user_id'] = $this->saved_steam_users[$leaderboard_entry['steam_user_id']];
                        }
        
                        db()->insert('leaderboard_entries', $leaderboard_entry, 'add_leaderboard_entry');
                    }
                }
            }
        }
        
        apcu_clear_cache('user');
    }
    
    public function saveLeaderboardEntriesToCache($lbid) {
        if(!empty($this->imported_leaderboard_entries[$lbid])) {
            $leaderboard_record = $this->imported_leaderboards[$lbid];
            $leaderboard_id = $leaderboard_record['leaderboard_id'];
            
            $leaderboard_entries_hash_name = "leaderboard:{$leaderboard_id}:entries";
            $leaderboard_entries_hash_name_old = "{$leaderboard_entries_hash_name}_old";
            $leaderboard_entries_hash_name_new = "{$leaderboard_entries_hash_name}_new";
            
            $power_leaderboard_entries_hash_name = "power_leaderboard:{$leaderboard_id}:entries";
            $power_leaderboard_entries_hash_name_old = "{$power_leaderboard_entries_hash_name}_old";
            $power_leaderboard_entries_hash_name_new = "{$power_leaderboard_entries_hash_name}_new";
            
            $transaction = $this->cache->multi();
        
            foreach($this->imported_leaderboard_entries[$lbid] as &$leaderboard_entry) {
                $leaderboard_entry_id = md5(uniqid(mt_rand(), true));
            
                $leaderboard_entry['leaderboard_entry_id'] = $leaderboard_entry_id;
                
                if(!empty($this->saved_steam_users[$leaderboard_entry['steam_user_id']])) {
                    $leaderboard_entry['steam_user_id'] = $this->saved_steam_users[$leaderboard_entry['steam_user_id']];
                }
            
                $hash_name = "leaderboards:{$leaderboard_id}:entries:{$leaderboard_entry_id}";
        
                $transaction->hMset($hash_name, $leaderboard_entry);
                $transaction->rPush($leaderboard_entries_hash_name_new, $hash_name);

                if(!empty($leaderboard_record['is_power_ranking'])) {
                    $transaction->rPush($power_leaderboard_entries_hash_name_new, $hash_name);
                }
            }
            
            $transaction->rename($leaderboard_entries_hash_name, $leaderboard_entries_hash_name_old);
            $transaction->rename($leaderboard_entries_hash_name_new, $leaderboard_entries_hash_name);
            
            $transaction->rename($power_leaderboard_entries_hash_name, $power_leaderboard_entries_hash_name_old);
            $transaction->rename($power_leaderboard_entries_hash_name_new, $power_leaderboard_entries_hash_name);
            
            $transaction->exec();
            
            $old_leaderboard_entry_keys = $this->cache->lRange($leaderboard_entries_hash_name_old, 0, -1);
            
            if(!empty($old_leaderboard_entry_keys)) {
                $transaction = $this->cache->multi();
            
                foreach($old_leaderboard_entry_keys as &$old_leaderboard_entry_key) {
                    $transaction->delete($old_leaderboard_entry_key);
                }
                
                $transaction->delete($leaderboard_entries_hash_name_old);
                $transaction->delete($power_leaderboard_entries_hash_name_old);
                
                $transaction->exec();
            }
            
            unset($this->imported_leaderboard_entries[$lbid]);
        }
    }
}