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
}