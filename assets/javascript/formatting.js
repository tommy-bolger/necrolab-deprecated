function Formatting() {}

/* ---------- Necrolab ---------- */

Formatting.getNecrolabApiUrl = function(uri) {
    return 'https://api.necrolab.com' + uri;
};

Formatting.getNecrolabUserUrl = function(steamid) {
    return '/players/player?id=' + steamid;
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
        rounded_number = null;
    }

    return rounded_number;
}

Formatting.convertSecondsToTime = function(seconds) {
    var parsed_time = null;
    
    if(seconds != null) {
        seconds = parseFloat(seconds);
        
        if(seconds > 0) {
            //Solution found at: http://stackoverflow.com/a/31340408
            var format = 'mm:ss.SS';
            
            if(seconds >= 3600) {
                format = 'H:' + format;
            }
            else {
                format = '00:' + format;
            }
            
            parsed_time = moment("2015-01-01").startOf('day').millisecond(seconds * 1000).format(format);
        }
    }
    
    return parsed_time;
};

Formatting.addCommasToNumber = function(unformatted_number) {
    var formatted_number = null;
    
    if(unformatted_number != null) {
        formatted_number = unformatted_number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    return formatted_number;
};

Formatting.getReplayFileHtml = function(replay_file_url) {
    var replay_file_html = null;
    
    if(replay_file_url != null) {
        replay_file_html = '<a href="' + replay_file_url + '">Download</a>';
    }
    
    return replay_file_html;
};

Formatting.getAchievementIconHtml = function(icon_url) {
    var icon_html = null;
    
    if(icon_url != null) {
        icon_html = '<img src="' + icon_url + '" />';
    }
    
    return icon_html;
};

Formatting.addNoWrapHtml = function(wrapped) {
    var unwrapped_html = null;
    
    if(wrapped != null) {
        unwrapped_html = '<span class="no_wrap">' + wrapped + '</span>';
    }
    
    return unwrapped_html;
};

/* ---------- Characters ---------- */

Formatting.getCharacterImagePlaceholderUrl = function() {
    return '/assets/images/modules/necrolab/styles/default/characters/character_placeholder.png';
};

Formatting.getCharacterImageHtml = function(character_name) {
    return '<img class="character_header ' + character_name + '_header" src="' + Formatting.getCharacterImagePlaceholderUrl() + '" />';
};

/* ---------- Steam ---------- */

Formatting.getSteamLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/share_steam_logo_small.png" />';
};

Formatting.getSteamLogoLink = function(personaname, profile_url) {
    var link_html = null;
    
    if(profile_url != null) {
        link_html = '<a href="' + profile_url + '" target="_blank">' + Formatting.getSteamLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getSteamFancyLink = function(personaname, profile_url) {    
    var link_html = null;
    
    if(profile_url != null) {
        link_html = '<span class="no_wrap">' + '<a href="' + profile_url + '" target="_blank">' + Formatting.getSteamLogo() + '&nbsp' + personaname + '</a>' + '</span>';
    }
    
    return link_html;
};

/* ---------- Twitch ---------- */

Formatting.getTwitchUrl = function(twitch_username) {
    return 'https://www.twitch.tv/' + twitch_username;
};

Formatting.getTwitchLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/twitch_small.png" />';
};

Formatting.getTwitchUsernameLink = function(twitch_username) {
    var link_html = null;
    
    if(twitch_username != null) {
        link_html = '<a href="' + Formatting.getTwitchUrl(twitch_username) + '" target="_blank">' + twitch_username + '</a>';
    }
    
    return link_html;
};

Formatting.getTwitchLogoLink = function(twitch_username) {
    var link_html = null;
    
    if(twitch_username != null) {
        link_html = '<a href="' + Formatting.getTwitchUrl(twitch_username) + '" target="_blank">' + Formatting.getTwitchLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getTwitchFancyLink = function(twitch_username) {
    var link_html = null;
    
    if(twitch_username != null) {
        link_html = '<span class="no_wrap"><a href="' + Formatting.getTwitchUrl(twitch_username) + '">' + Formatting.getTwitchLogo() + '&nbsp;' + twitch_username + '</a></span>';
    }
    
    return link_html;
};

Formatting.getTwitchLoginLink = function() {    
    return link_html = '<a href="/players/player/login/twitch"><img src="/assets/images/modules/necrolab/styles/default/connections/ConnectWithTwitch.png" /></a>';
};

/* ---------- Twitter ---------- */

Formatting.getTwitterUrl = function(twitter_username) {
    return 'https://www.twitter.com/' + twitter_username;
};

Formatting.getTwitterLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/twitter_logo_blue_small.png" />';
};

Formatting.getTwitterUsernameLink = function(twitter_username) {
    var link_html = null;
    
    if(twitter_username != null) {
        link_html = '<a href="' + Formatting.getTwitterUrl(twitter_username) + '" target="_blank">' + twitter_username + '</a>';
    }
    
    return link_html;
};

Formatting.getTwitterLogoLink = function(twitter_username) {
    var link_html = null;
    
    if(twitter_username != null) {
        link_html = '<a href="' + Formatting.getTwitterUrl(twitter_username) + '" target="_blank">' + Formatting.getTwitterLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getTwitterFancyLink = function(twitter_username) {
    var link_html = null;
    
    if(twitter_username != null) {
        link_html = '<span class="no_wrap"><a href="' + Formatting.getTwitterUrl(twitter_username) + '" target="_blank">' + Formatting.getTwitterLogo() + '&nbsp;' + twitter_username + '</a></span>';
    }
    
    return link_html;
};

Formatting.getTwitterLoginLink = function() {    
    return link_html = '<a href="/players/player/login/twitter"><img src="/assets/images/modules/necrolab/styles/default/connections/ConnectWithTwitter.png" /></a>';
};

/* ---------- Hitbox ---------- */

Formatting.getHitboxUrl = function(hitbox_username) {
    return 'https://www.hitbox.com/' + hitbox_username;
};

Formatting.getHitboxLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/hitboxicongreen_small.png" />';
};

Formatting.getHitboxUsernameLink = function(hitbox_username) {
    var link_html = null;
    
    if(hitbox_username != null) {
        link_html = '<a href="' + Formatting.getHitboxUrl(hitbox_username) + '" target="_blank">' + hitbox_username + '</a>';
    }
    
    return link_html;
};

Formatting.getHitboxLogoLink = function(hitbox_username) {
    var link_html = null;
    
    if(hitbox_username != null) {
        link_html = '<a href="' + Formatting.getHitboxUrl(hitbox_username) + '" target="_blank">' + Formatting.getHitboxLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getHitboxFancyLink = function(hitbox_username) {
    var link_html = null;
    
    if(hitbox_username != null) {
        link_html = '<span class="no_wrap"><a href="' + Formatting.getHitboxUrl(hitbox_username) + '" target="_blank">' + Formatting.getHitboxLogo() + '&nbsp;' + hitbox_username + '</a></span>';
    }
    
    return link_html;
};

Formatting.getHitboxLoginLink = function() {    
    return link_html = '<a href="/players/player/login/hitbox">Connect with Hitbox</a>';
};

/* ---------- Beampro ---------- */

Formatting.getBeamproUrl = function(beampro_username) {
    return 'https://www.beam.pro/' + beampro_username;
};

Formatting.getBeamproLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/beampro-logo-ball-small.png" />';
};

Formatting.getBeamproUsernameLink = function(beampro_username) {
    var link_html = null;
    
    if(beampro_username != null) {
        link_html = '<a href="' + Formatting.getBeamproUrl(beampro_username) + '" target="_blank">' + beampro_username + '</a>';
    }
    
    return link_html;
};

Formatting.getBeamproLogoLink = function(beampro_username) {
    var link_html = null;
    
    if(beampro_username != null) {
        link_html = '<a href="' + Formatting.getBeamproUrl(beampro_username) + '" target="_blank">' + Formatting.getBeamproLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getBeamproFancyLink = function(beampro_username) {
    var link_html = null;
    
    if(beampro_username != null) {
        link_html = '<span class="no_wrap"><a href="' + Formatting.getBeamproUrl(beampro_username) + '" target="_blank">' + Formatting.getBeamproLogo() + '&nbsp;' + beampro_username + '</a></span>';
    }
    
    return link_html;
};

Formatting.getBeamproLoginLink = function() {    
    return link_html = '<a href="/players/player/login/beampro"><img src="/assets/images/modules/necrolab/styles/default/connections/ConnectWithBeam.png" /></a>';
};

/* ---------- Discord ---------- */

Formatting.getDiscordLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/discord_logo_white_small.png" />';
};

Formatting.getDiscordUserLogo = function(discord_username, discriminator) {
    var full_username = discord_username + '#' + discriminator;
    
    return '<a class="icon_popover" data-content="' + full_username + '"><img src="/assets/images/modules/necrolab/styles/default/logos/discord_logo_white_small.png" alt="' + full_username + '" title="' + full_username + '"/></a>';
};

Formatting.getDiscordFancyLink = function(discord_username, discriminator) {
    var link_html = null;
    
    if(discord_username != null) {
        link_html = '<span class="no_wrap">' + Formatting.getDiscordLogo() + '&nbsp;' + discord_username + '#' + discriminator + '</span>';
    }
    
    return link_html;
};

Formatting.getDiscordLoginLink = function() {    
    return link_html = '<a href="/players/player/login/discord"><img src="/assets/images/modules/necrolab/styles/default/connections/ConnectWithDiscord.png" /></a>';
};

/* ---------- Youtube ---------- */

Formatting.getYoutubeUrl = function(youtube_username) {
    return 'https://www.youtube.com/channel/' + youtube_username;
};

Formatting.getYoutubeLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/YouTube-social-small.png" />';
};

Formatting.getYoutubeUsernameLink = function(youtube_username) {
    var link_html = null;
    
    if(youtube_username != null) {
        link_html = '<a href="' + Formatting.getYoutubeUrl(youtube_username) + '" target="_blank">' + youtube_username + '</a>';
    }
    
    return link_html;
};

Formatting.getYoutubeLogoLink = function(youtube_username) {
    var link_html = null;
    
    if(youtube_username != null) {
        link_html = '<a href="' + Formatting.getYoutubeUrl(youtube_username) + '" target="_blank">' + Formatting.getYoutubeLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getYoutubeFancyLink = function(youtube_username) {
    var link_html = null;
    
    if(youtube_username != null) {
        link_html = '<span class="no_wrap"><a href="' + Formatting.getYoutubeUrl(youtube_username) + '" target="_blank">' + Formatting.getYoutubeLogo() + '&nbsp;' + youtube_username + '</a></span>';
    }
    
    return link_html;
};

Formatting.getYoutubeLoginLink = function() {    
    return link_html = '<a href="/players/player/login/youtube"><img src="/assets/images/modules/necrolab/styles/default/connections/ConnectWithYouTube.png" /></a>';
};

/* ---------- Reddit ---------- */

Formatting.getRedditUrl = function(reddit_username) {
    return 'https://www.reddit.com/u/' + reddit_username;
};

Formatting.getRedditLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/Reddit_small.png" />';
};

Formatting.getRedditUsernameLink = function(reddit_username) {
    var link_html = null;
    
    if(reddit_username != null) {
        link_html = '<a href="' + Formatting.getRedditUrl(reddit_username) + '" target="_blank">' + reddit_username + '</a>';
    }
    
    return link_html;
};

Formatting.getRedditLogoLink = function(reddit_username) {
    var link_html = null;
    
    if(reddit_username != null) {
        link_html = '<a href="' + Formatting.getRedditUrl(reddit_username) + '" target="_blank">' + Formatting.getRedditLogo() + '</a>';
    }
    
    return link_html;
};

Formatting.getRedditFancyLink = function(reddit_username) {
    var link_html = null;
    
    if(reddit_username != null) {
        link_html = '<span class="no_wrap"><a href="' + Formatting.getRedditUrl(reddit_username) + '" target="_blank">' + Formatting.getRedditLogo() + '&nbsp;' + reddit_username + '</a></span>';
    }
    
    return link_html;
};

Formatting.getRedditLoginLink = function() {    
    return link_html = '<a href="/players/player/login/reddit"><img src="/assets/images/modules/necrolab/styles/default/connections/ConnectWithReddit.png" /></a>';
};

/* ---------- Nico Nico ---------- */

Formatting.getNicoNicoLogo = function() {
    return '<img src="/assets/images/modules/necrolab/styles/default/logos/hitboxicongreen_small.png" />';
};

Formatting.getNicoNicoFancyLink = function(nico_nico_url) {
    var link_html = null;
    
    if(nico_nico_url != null) {
        link_html = '<span class="no_wrap"><a href="' + nico_nico_url + '" target="_blank">' + Formatting.getNicoNicoLogo() + '" /></a></span>';
    }
    
    return link_html;
};

/* ---------- External Website ---------- */

Formatting.getWebsiteFancyLink = function(website_url) {
    var link_html = null;
    
    if(website_url != null) {
        link_html = '<a href="' + website_url + '" target="_blank"><img src="/assets/images/modules/necrolab/styles/default/logos/external_link_small.png" /></a>';
    }
    
    return link_html;
};

Formatting.getSocialMedia = function(steamid, social_media) {
    var social_media_html = '<span class="no_wrap">';
    
    //Exclude this for now since it seems that steamids don't go to their exact profiles when linked.
    //social_media_html += Formatting.getSteamLogoLink(steamid);
    
    if(social_media['beampro']['username'] != null) {
        social_media_html += Formatting.getBeamproLogoLink(social_media.beampro.username);
    }
    
    if(social_media['discord']['username'] != null) {
        social_media_html += Formatting.getDiscordUserLogo(social_media.discord.username, social_media.discord.discriminator);
    }
    
    if(social_media['reddit']['username'] != null) {
        social_media_html += Formatting.getRedditLogoLink(social_media.reddit.username);
    }
    
    if(social_media['twitch']['username'] != null) {
        social_media_html += Formatting.getTwitchLogoLink(social_media.twitch.username);
    }
    
    if(social_media['twitter']['nickname'] != null) {
        social_media_html += Formatting.getTwitterLogoLink(social_media.twitter.nickname);
    }
    
    if(social_media['youtube']['username'] != null) {
        social_media_html += Formatting.getYoutubeLogoLink(social_media.youtube.username);
    }
    
    social_media_html += '</span>';
    
    return social_media_html;
};

/* ---------- Table Titles ---------- */

Formatting.getCharacterDisplay = function(character_name) {
    var character_display = '';
    
    if(character_name == 'all') {
        character_display += '<span class="menu_small">All Chars</span>';
    }
    else if(character_name == 'all_dlc') {
        character_display += '<span class="menu_small">All Chars DLC</span>';
    }
    else if(character_name == 'story') {
        character_display += '<span class="menu_small">Story</span>';
    }
    else {
        character_display += Formatting.getCharacterImageHtml(character_name);
    }
    
    return character_display;
}

Formatting.getLeaderboardEntriesTitle = function(leaderboard_record) {    
    var table_title = Formatting.getCharacterDisplay(leaderboard_record.character);
    
    if(leaderboard_record['release'] != null) {
        table_title += ' ' + leaderboard_record.release.display_name;
    }
    
    if(leaderboard_record['mode'] != null) {
        table_title += ' ' + leaderboard_record.mode.display_name + ' Mode ';
    }
    
    table_title += ' ';
    
    if(leaderboard_record.is_speedrun == 1) {
        table_title += 'Speedrun';
    }
    else if(leaderboard_record.is_deathless == 1) {
        table_title += 'Deathless';
    }
    else {
        table_title += 'Score'; 
    }
    
    table_title += ' ' + Formatting.getLeaderboardEntriesShortTitle(leaderboard_record);
    
    return table_title;
};

Formatting.getLeaderboardEntriesShortTitle = function(leaderboard_record, character_name) {    
    var table_title = '';
    
    if(leaderboard_record.is_co_op == 0) {
        if(leaderboard_record.is_seeded == 0) {
            if(leaderboard_record.is_custom == 0) {
                table_title += 'All Zones';
            }
            else {
                table_title += 'Custom Music';
            }
        }
        else {
            if(leaderboard_record.is_custom == 0) {
                table_title += 'Seeded';
            }
            else {
                table_title += 'Seeded Custom Music';
            }
        }
    }
    else {
        if(leaderboard_record.is_seeded == 0) {
            if(leaderboard_record.is_custom == 0) {
                table_title += 'Co-Op';
            }
            else {
                table_title += 'Co-Op Custom Music';
            }
        }
        else {
            if(leaderboard_record.is_custom == 0) {
                table_title += 'Seeded Co-Op';
            }
            else {
                table_title += 'Seeded Co-Op Custom Music';
            }
        }
    }
    
    return table_title;
};