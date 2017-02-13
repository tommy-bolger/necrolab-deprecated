function Formatting() {}

/* ---------- Necrolab ---------- */

Formatting.getNecrolabUserUrl = function(steamid) {
    return '/players/player/?id=' + steamid;
};

Formatting.getNecrolabUserLink = function(steamid, personaname) {
    return '<a href="' + Formatting.getNecrolabUserUrl(steamid) + '">' + personaname + '</a>';
};

Formatting.roundNumber = function(unrounded_number) {
    var rounded_number;
    
    if(unrounded_number != null) {
        var rounding_place = 1;
        var unrounded_number_split = unrounded_number.toString().split('.');
        
        var left_decimal = unrounded_number_split[0];
        var right_decimal = parseInt(unrounded_number_split[1]);
        
        var left_decimal_length = left_decimal.length;
    
        if(left_decimal_length == 2) {
            rounding_place = 2;
        }
        
        if(left_decimal_length == 1) {
            left_decimal = parseInt(left_decimal);
        
            if(left_decimal < 1) {
                rounding_place = 5;
            }
            else {
                rounding_place = 3;
            }
        }
        
        var casted_unrounded_number = Number(unrounded_number);
        
        rounded_number = casted_unrounded_number.toFixed(rounding_place);
    }
    else {
        rounded_number = '&nbsp;';
    }

    return rounded_number;
}

/* ---------- Characters ---------- */

Formatting.getCharacterImagePlaceholderUrl = function() {
    return '/assets/images/modules/necrolab/styles/default/characters/character_placeholder.png';
};

Formatting.getCharacterImageHtml = function(character_name) {
    return '<img class="character_header ' + character_name + '_header" src="' + Formatting.getCharacterImagePlaceholderUrl() + '" />';
};

/* ---------- Steam ---------- */

Formatting.getSteamProfileUrl = function(steamid) {
    return 'http://steamcommunity.com/profiles/' + steamid;
};

Formatting.getSteamLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/share_steam_logo_small.png" />';
};

Formatting.getSteamLogoLink = function(steamid, personaname) {
    return '<a href="' + Formatting.getSteamProfileUrl(steamid) + '" target="_blank">' + Formatting.getSteamLogo() + '</a>';
};

Formatting.getSteamFancyLink = function(steamid, personaname) {
    return '<a href="' + Formatting.getSteamProfileUrl(steamid) + '" target="_blank">' + Formatting.getSteamLogo() + '&nbsp' + personaname + '</a>';
};

/* ---------- Twitch ---------- */

Formatting.getTwitchUrl = function(twitch_username) {
    return 'https://www.twitch.tv/' + twitch_username;
};

Formatting.getTwitchLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/twitch_small.png" />';
};

Formatting.getTwitchUsernameLink = function(twitch_username) {
    var link_html = '&nbsp;';
    
    if(twitch_username != null) {
        link_html = '<a href="' + Formatting.getTwitchUrl(twitch_username) + '" target="_blank">' + twitch_username + '</a>';
    }
    
    return link_html;
};

Formatting.getTwitchLogoLink = function(twitch_username) {
    var link_html = '&nbsp;';
    
    if(twitch_username != null) {
        link_html = '<a href="' + Formatting.getTwitchUrl(twitch_username) + '" target="_blank">' + Formatting.getTwitchLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getTwitchFancyLink = function(twitch_username) {
    var link_html = '&nbsp;';
    
    if(twitch_username != null) {
        link_html = '<a href="' + Formatting.getTwitchUrl(twitch_username) + '" target="_blank">' + Formatting.getTwitchLogo() + '&nbsp;' + twitch_username + '</a>';
    }
    
    return link_html;
};

/* ---------- Twitter ---------- */

Formatting.getTwitterUrl = function(twitter_username) {
    return 'https://www.twitter.com/' + twitter_username;
};

Formatting.getTwitterLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/twitter_logo_blue_small.png" />';
};

Formatting.getTwitterUsernameLink = function(twitter_username) {
    var link_html = '&nbsp;';
    
    if(twitter_username != null) {
        link_html = '<a href="' + Formatting.getTwitterUrl(twitter_username) + '" target="_blank">' + twitter_username + '</a>';
    }
    
    return link_html;
};

Formatting.getTwitterLogoLink = function(twitter_username) {
    var link_html = '&nbsp;';
    
    if(twitter_username != null) {
        link_html = '<a href="' + Formatting.getTwitterUrl(twitter_username) + '" target="_blank">' + Formatting.getTwitterLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getTwitterFancyLink = function(twitter_username) {
    var link_html = '&nbsp;';
    
    if(twitter_username != null) {
        link_html = '<a href="' + Formatting.getTwitterUrl(twitter_username) + '" target="_blank">' + Formatting.getTwitterLogo() + '&nbsp;' + twitter_username + '</a>';
    }
    
    return link_html;
};

/* ---------- Hitbox ---------- */

Formatting.getHitboxUrl = function(hitbox_username) {
    return 'https://www.hitbox.com/' + hitbox_username;
};

Formatting.getHitboxLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/hitboxicongreen_small.png" />';
};

Formatting.getHitboxUsernameLink = function(hitbox_username) {
    var link_html = '&nbsp;';
    
    if(hitbox_username != null) {
        link_html = '<a href="' + Formatting.getHitboxUrl(hitbox_username) + '" target="_blank">' + hitbox_username + '</a>';
    }
    
    return link_html;
};

Formatting.getHitboxLogoLink = function(hitbox_username) {
    var link_html = '&nbsp;';
    
    if(hitbox_username != null) {
        link_html = '<a href="' + Formatting.getHitboxUrl(hitbox_username) + '" target="_blank">' + Formatting.getHitboxLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getHitboxFancyLink = function(hitbox_username) {
    var link_html = '&nbsp;';
    
    if(hitbox_username != null) {
        link_html = '<a href="' + Formatting.getHitboxUrl(hitbox_username) + '" target="_blank">' + Formatting.getHitboxLogo() + '&nbsp;' + hitbox_username + '</a>';
    }
    
    return link_html;
};

/* ---------- Beampro ---------- */

Formatting.getBeamproUrl = function(beampro_username) {
    return 'https://www.beam.pro/' + beampro_username;
};

Formatting.getBeamproLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/beampro-logo-ball-small.png" />';
};

Formatting.getBeamproUsernameLink = function(beampro_username) {
    var link_html = '&nbsp;';
    
    if(beampro_username != null) {
        link_html = '<a href="' + Formatting.getBeamproUrl(beampro_username) + '" target="_blank">' + beampro_username + '</a>';
    }
    
    return link_html;
};

Formatting.getBeamproLogoLink = function(beampro_username) {
    var link_html = '&nbsp;';
    
    if(beampro_username != null) {
        link_html = '<a href="' + Formatting.getBeamproUrl(beampro_username) + '" target="_blank">' + Formatting.getBeamproLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getBeamproFancyLink = function(beampro_username) {
    var link_html = '&nbsp;';
    
    if(beampro_username != null) {
        link_html = '<a href="' + Formatting.getBeamproUrl(beampro_username) + '" target="_blank">' + Formatting.getBeamproLogo() + '&nbsp;' + beampro_username + '</a>';
    }
    
    return link_html;
};

/* ---------- Discord ---------- */

Formatting.getDiscordLogo = function(discord_username, discriminator) {
    var full_username = discord_username + '#' + discriminator;
    
    return '<a href="http://discord/' + full_username + '"><img src="/assets/images/modules/necrolab/styles/default/logos/discord_logo_white_small.png" alt="' + full_username + '" /></a>';
};

Formatting.getDiscordFancyLink = function(discord_username, discriminator) {
    var link_html = '&nbsp;';
    
    if(discord_username != null) {
        link_html = '<span class="no_wrap">' + Formatting.getDiscordLogo() + '&nbsp;' + discord_username + '#' + discriminator + '</span>';
    }
    
    return link_html;
};

/* ---------- Youtube ---------- */

Formatting.getYoutubeUrl = function(youtube_username) {
    return 'https://www.youtube.com/channel/' + youtube_username;
};

Formatting.getYoutubeLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/YouTube-social-small.png" />';
};

Formatting.getYoutubeUsernameLink = function(youtube_username) {
    var link_html = '&nbsp;';
    
    if(youtube_username != null) {
        link_html = '<a href="' + Formatting.getYoutubeUrl(youtube_username) + '" target="_blank">' + youtube_username + '</a>';
    }
    
    return link_html;
};

Formatting.getYoutubeLogoLink = function(youtube_username) {
    var link_html = '&nbsp;';
    
    if(youtube_username != null) {
        link_html = '<a href="' + Formatting.getYoutubeUrl(youtube_username) + '" target="_blank">' + Formatting.getYoutubeLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getYoutubeFancyLink = function(youtube_username) {
    var link_html = '&nbsp;';
    
    if(youtube_username != null) {
        link_html = '<a href="' + Formatting.getYoutubeUrl(youtube_username) + '" target="_blank">' + Formatting.getYoutubeLogo() + '&nbsp;' + youtube_username + '</a>';
    }
    
    return link_html;
};

/* ---------- Reddit ---------- */

Formatting.getRedditUrl = function(reddit_username) {
    return 'https://www.reddit.com/u/' + reddit_username;
};

Formatting.getRedditLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/Reddit_small.png" />';
};

Formatting.getRedditUsernameLink = function(reddit_username) {
    var link_html = '&nbsp;';
    
    if(reddit_username != null) {
        link_html = '<a href="' + Formatting.getRedditUrl(reddit_username) + '" target="_blank">' + reddit_username + '</a>';
    }
    
    return link_html;
};

Formatting.getRedditLogoLink = function(reddit_username) {
    var link_html = '&nbsp;';
    
    if(reddit_username != null) {
        link_html = '<a href="' + Formatting.getRedditUrl(reddit_username) + '" target="_blank">' + Formatting.getRedditLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getRedditFancyLink = function(reddit_username) {
    var link_html = '&nbsp;';
    
    if(reddit_username != null) {
        link_html = '<a href="' + Formatting.getRedditUrl(reddit_username) + '" target="_blank">' + Formatting.getRedditLogo() + '&nbsp;' + reddit_username + '</a>';
    }
    
    return link_html;
};

/* ---------- Nico Nico ---------- */

Formatting.getNicoNicoLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/hitboxicongreen_small.png" />';
};

Formatting.getNicoNicoFancyLink = function(nico_nico_url) {
    var link_html = '&nbsp;';
    
    if(nico_nico_url != null) {
        link_html = '<a href="' + nico_nico_url + '" target="_blank">' + Formatting.getNicoNicoLogo() + '" /></a>';
    }
    
    return link_html;
};

/* ---------- External Website ---------- */

Formatting.getWebsiteFancyLink = function(website_url) {
    var link_html = '&nbsp;';
    
    if(website_url != null) {
        link_html = '<a href="' + website_url + '" target="_blank"><img src="/assets/images/modules/necrolab/styles/default/logos/external_link_small.png" /></a>';
    }
    
    return link_html;
};

Formatting.getSocialMedia = function(steamid, social_media) {
    var social_media_html = '<span class="no_wrap">';
    
    //Exclude this for now since it seems that steamids don't go to their exact profiles when linked.
    //social_media_html += Formatting.getSteamLogoLink(steamid);
    
    if(social_media['beampro'] != null) {
        social_media_html += Formatting.getBeamproLogoLink(social_media.beampro);
    }
    
    if(social_media['discord']['username'] != null) {
        social_media_html += Formatting.getDiscordLogo(social_media.discord.username, social_media.discord.discriminator);
    }
    
    if(social_media['hitbox'] != null) {
        social_media_html += Formatting.getHitboxLogoLink(social_media.hitbox);
    }
    
    if(social_media['reddit'] != null) {
        social_media_html += Formatting.getRedditLogoLink(social_media.reddit);
    }
    
    if(social_media['twitch'] != null) {
        social_media_html += Formatting.getTwitchLogoLink(social_media.twitch);
    }
    
    if(social_media['twitter']['name'] != null) {
        social_media_html += Formatting.getTwitterLogoLink(social_media.twitter.name);
    }
    
    /*if(social_media['nico_nico_url'] != null) {
        social_media_html += Formatting.getNicoNicoFancyLink(social_media.nico_nico_url);
    }*/
    
    if(social_media['youtube'] != null) {
        social_media_html += Formatting.getYoutubeLogoLink(social_media.youtube);
    }
    
    if(social_media['website'] != null) {
        social_media_html += Formatting.getWebsiteFancyLink(social_media.website);
    }
    
    social_media_html += '</span>';
    
    return social_media_html;
};