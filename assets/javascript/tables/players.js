function process_player_data(data, players_table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    Formatting.getNecrolabUserLink(row_data.steamid, row_data.personaname),
                    Formatting.getSteamLogoLink(row_data.steamid, row_data.personaname),
                    Formatting.getTwitchFancyLink(row_data.twitch_username),
                    Formatting.getTwitterFancyLink(row_data.twitter_username),
                    Formatting.getHitboxFancyLink(row_data.hitbox_username),
                    Formatting.getNicoNicoFancyLink(row_data.nico_nico_url),
                    Formatting.getWebsiteFancyLink(row_data.website)
                ]);
            }
        }
    }
    
    return processed_data;
};

$(document).ready(function() {
    var players_table = new NecroTable($('#entries_table'));
    
    players_table.enableLengthMenu();
    players_table.enableButtons();
    players_table.enablePaging();
    players_table.enableSiteField();
    
    players_table.setAjaxUrl('/api/players');
    
    players_table.addColumns([
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
            name: 'hitbox_username',
            title: 'Hitbox',
            type: 'string'
        },
        {
            name: 'nico_nico_url',
            title: '<span class="no_wrap">Nico Nico</span>',
            type: 'string'
        },
        {
            name: 'website',
            title: 'Website',
            type: 'string'
        }
    ]);
    
    players_table.setDataProcessCallback(window, 'process_player_data');
    
    players_table.render();
});