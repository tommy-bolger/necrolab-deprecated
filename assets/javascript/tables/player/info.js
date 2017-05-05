function process_info_data(data, table) {
    var processed_data = [];
    
    
    if(data['steamid'] != null) {
        var beampro_info = Formatting.getBeamproFancyLink(data.linked.beampro.username);
        var discord_info = Formatting.getDiscordFancyLink(data.linked.discord.username, data.linked.discord.discriminator);
        var reddit_info = Formatting.getRedditFancyLink(data.linked.reddit.username);
        var twitch_info = Formatting.getTwitchFancyLink(data.linked.twitch.username);
        var twitter_info = Formatting.getTwitterFancyLink(data.linked.twitter.nickname);
        var youtube_info = Formatting.getYoutubeFancyLink(data.linked.youtube.username);
        
        if(NecroTable.user_api_key != null) {
            if(beampro_info == null) {
                beampro_info = Formatting.getBeamproLoginLink();
            }
            
            if(discord_info == null) {
                discord_info = Formatting.getDiscordLoginLink();
            }
            
            if(reddit_info == null) {
                reddit_info = Formatting.getRedditLoginLink();
            }
            
            if(twitch_info == null) {
                twitch_info = Formatting.getTwitchLoginLink();
            }
            
            if(twitter_info == null) {
                twitter_info = Formatting.getTwitterLoginLink();
            }
            
            if(youtube_info == null) {
                youtube_info = Formatting.getYoutubeLoginLink();
            }
        }
        
        processed_data.push([
            Formatting.getNecrolabUserLink(data.steamid, data.personaname),            
            Formatting.getSteamFancyLink(data.linked.steam.personaname, data.linked.steam.profile_url),
            beampro_info,
            discord_info,
            reddit_info,
            twitch_info,
            twitter_info,
            youtube_info
        ]);
    }
    
    return processed_data;
};

function initialize_info_table() {
    var table = new NecroTable($('#player_info_table'));
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'personaname',
            title: 'Player',
            type: 'string'
        },
        {
            name: 'steamid',
            title: 'Steam',
            type: 'string',
            orderable: false
        },
        {
            name: 'beampro_username',
            title: 'Beam.pro',
            type: 'string'
        },
        {
            name: 'discord_username',
            title: 'Discord',
            type: 'string'
        },
        {
            name: 'reddit_username',
            title: 'Reddit',
            type: 'string'
        },
        {
            name: 'twitch_username',
            title: 'Twitch',
            type: 'string'
        },
        {
            name: 'twitter_username',
            title: 'Twitter',
            type: 'string'
        },
        {
            name: 'youtube_username',
            title: 'Youtube',
            type: 'string'
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_info_data');
    
    table.render();
};