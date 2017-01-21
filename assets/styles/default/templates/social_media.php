<?php
$social_media = '';

if(!empty($this->twitch_username)) {
    $social_media .= "<a href=\"http://www.twitch.tv/{$this->twitch_username}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/twitch_small.png\" alt=\"Twitch Channel for {$this->personaname}\" /></a>";
}

if(!empty($this->nico_nico_url)) {
    $social_media .= "<a href=\"{$this->nico_nico_url}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/nico_nico_small.png\" alt=\"Nico Nico Channel for {$this->personaname}\" /></a>";
}

if(!empty($this->hitbox_username)) {
    $social_media .= "<a href=\"http://www.hitbox.tv/{$this->hitbox_username}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/hitboxicongreen_small.png\" alt=\"Hitbox Channel for {$this->personaname}\" /></a>";
}

if(!empty($this->twitter_username)) {
    $social_media .= "<a href=\"http://www.twitter.com/{$this->twitter_username}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/twitter_logo_blue_small.png\" alt=\"Twitter Feed for {$this->personaname}\" /></a>";
}

if(!empty($this->website)) {
    $website_url = $this->website;
    
    if(strpos($website_url, 'http://') === false && strpos($website_url, 'https://') === false) {
        $website_url = "http://{$website_url}";
    }

    $social_media .= "<a href=\"{$website_url}\" target=\"_blank\"><img src=\"/assets/images/modules/necrolab/external_link_small.png\" alt=\"Website of {$this->personaname}\" /></a>";
}

if(empty($social_media)) {
    $social_media = '&nbsp';
}

echo $social_media;