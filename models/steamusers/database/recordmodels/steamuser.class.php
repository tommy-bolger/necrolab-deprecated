<?php

namespace Modules\Necrolab\Models\SteamUsers\Database\RecordModels;

use \Exception;
use \Framework\Core\RecordModel;

class SteamUser
extends RecordModel {    
    protected $steamid;

    protected $communityvisibilitystate;

    protected $profilestate;

    protected $personaname;

    protected $lastlogoff;

    protected $profileurl;

    protected $avatar;

    protected $avatarmedium;

    protected $avatarfull;

    protected $personastate;

    protected $realname;

    protected $primaryclanid;

    protected $timecreated;

    protected $personastateflags;

    protected $loccountrycode;

    protected $locstatecode;

    protected $loccityid;

    protected $updated;
    
    protected $twitch_username;
    
    protected $twitter_username;
    
    protected $nico_nico_url;
    
    protected $hitbox_username;
    
    protected $website;
    
    protected $twitch_user_id;
    
    protected $reddit_user_id;
    
    protected $discord_user_id;
    
    protected $youtube_user_id;
    
    protected $twitter_user_id;
    
    protected $beampro_user_id;
    
    public function getTempRecordArray() {
        return array(
            'communityvisibilitystate' => $this->communityvisibilitystate,
            'profilestate' => $this->profilestate,
            'personaname' => $this->personaname,
            'lastlogoff' => $this->lastlogoff,
            'profileurl' => $this->profileurl,
            'avatar' => $this->avatar,
            'avatarmedium' => $this->avatarmedium,
            'avatarfull' => $this->avatarfull,
            'personastate' => $this->personastate,
            'realname' => $this->realname,
            'primaryclanid' => $this->primaryclanid,
            'timecreated' => $this->timecreated,
            'personastateflags' => $this->personastateflags,
            'loccountrycode' => $this->loccountrycode,
            'locstatecode' => $this->locstatecode,
            'loccityid' => $this->loccityid,
            'updated' => $this->updated
        );
    }
}