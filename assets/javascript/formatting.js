function Formatting() {}

Formatting.getSteamProfileUrl = function(steamid) {
    return 'http://steamcommunity.com/profiles/' + steamid;
};

Formatting.getSteamProfileLink = function(steamid, personaname) {
    return '<a href="' + Formatting.getSteamProfileUrl(steamid) + '" target="_blank">Steam</a>';
};

Formatting.getNecrolabUserUrl = function(steamid) {
    return '/players/player/?id=' + steamid;
};

Formatting.getNecrolabUserLink = function(steamid, personaname) {
    return '<a href="' + Formatting.getNecrolabUserUrl(steamid) + '">' + personaname + '</a>';
};

Formatting.getTwitchLink = function(twitch_username) {
    var link_html = '&nbsp;';
    
    if(twitch_username != null) {
        link_html = '<a href="https://www.twitch.tv/' + twitch_username + '" target="_blank">' + twitch_username + '</a>';
    }
    
    return link_html;
};

Formatting.getTwitterLink = function(twitter_username) {
    var link_html = '&nbsp;';
    
    if(twitter_username != null) {
        link_html = '<a href="https://www.twitter.com/' + twitter_username + '" target="_blank">' + twitter_username + '</a>';
    }
    
    return link_html;
};

Formatting.getHitboxLink = function(hitbox_username) {
    var link_html = '&nbsp;';
    
    if(hitbox_username != null) {
        link_html = '<a href="http://www.hitbox.tv/' + hitbox_username + '" target="_blank">' + hitbox_username + '</a>';
    }
    
    return link_html;
};

Formatting.getNicoNicoLink = function(nico_nico_url) {
    var link_html = '&nbsp;';
    
    if(nico_nico_url != null) {
        link_html = '<a href="' + nico_nico_url + '" target="_blank">Nico Nico</a>';
    }
    
    return link_html;
};

Formatting.getWebsiteLink = function(website_url) {
    var link_html = '&nbsp;';
    
    if(website_url != null) {
        link_html = '<a href="' + website_url + '" target="_blank">Website</a>';
    }
    
    return link_html;
};