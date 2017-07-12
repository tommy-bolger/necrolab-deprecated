function process_player_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    Formatting.getNecrolabUserLink(row_data.steamid, row_data.personaname),
                    Formatting.getSteamFancyLink(row_data.linked.steam.personaname, row_data.linked.steam.profile_url),
                    Formatting.getBeamproFancyLink(row_data.linked.beampro.username),
                    Formatting.getDiscordFancyLink(row_data.linked.discord.username, row_data.linked.discord.discriminator),
                    Formatting.getRedditFancyLink(row_data.linked.reddit.username),
                    Formatting.getTwitchFancyLink(row_data.linked.twitch.username),
                    Formatting.getTwitterFancyLink(row_data.linked.twitter.nickname),
                    Formatting.getYoutubeFancyLink(row_data.linked.youtube.username)
                ]);
            }
        }
    }
    
    return processed_data;
};

$(document).ready(function() {
    var table = new NecroTable($('#entries_table'));
    
    table.enableFixedHeader();
    table.enableButtons();
    table.enablePaging();
    table.enableHistory();
    table.enableSearchField();
    table.enableSiteField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players'));
    
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
    
    table.setDataProcessCallback(window, 'process_player_data');
    
    table.render();
});