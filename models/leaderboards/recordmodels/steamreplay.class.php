<?php

namespace Modules\Necrolab\Models\Leaderboards\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;
use \Modules\Necrolab\Models\Leaderboards\Replays;

class SteamReplay
extends RecordModel {
    protected $seed;
    
    public function setPropertiesFromArray(array $property_values, $error_on_invalid_property = false) {}
    
    public function setPropertiesFromIndexedArray(array $indexed_property_values) {}
    
    public function setPropertiesFromObject($property_values, $error_on_invalid_property = false) {}
    
    public function setPropertiesFromReplayData($replay_file_data) {
        $replay_file_split = explode('%*#%*', $replay_file_data);
        
        if(count($replay_file_split) == 2) {
            $replay_data = $replay_file_split[1];
            
            $replay_data_segments = explode('\\n', $replay_data);
        
            $this->seed = Replays::getSeedFromZ1Seed($replay_data_segments[10]);
        }
    }
    
    public function setPropertiesFromReplayFile($replay_file_path) {
        $this->setPropertiesFromReplayData(Replays::getFile($replay_file_path));
    }
}