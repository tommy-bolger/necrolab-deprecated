function process_power_data(data, players_table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    row_data.rank,
                    Formatting.getSocialMedia(row_data.player.steamid, row_data.player.linked),
                    Formatting.getNecrolabUserLink(row_data.player.steamid, row_data.player.personaname),
                    row_data.score.rank,
                    Formatting.roundNumber(row_data.score.rank_points),
                    row_data.speed.rank,
                    Formatting.roundNumber(row_data.speed.rank_points),
                    row_data.deathless.rank,
                    Formatting.roundNumber(row_data.deathless.rank_points)
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
    players_table.enableReleaseField();
    players_table.enableDateField();
    players_table.enableSiteField();
    
    players_table.setAjaxUrl('/api/rankings/power/entries');
    
    players_table.addColumns([
        {
            name: 'rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'social_media',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'personaname',
            title: 'Player',
            type: 'string'
        },
        {
            name: 'score_rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'score_rank_points',
            title: 'Points',
            type: 'num-fmt'
        },
        {
            name: 'speed_rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'speed_rank_points',
            title: 'Points',
            type: 'num-fmt'
        },
        {
            name: 'deathless_rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'deathless_rank_points',
            title: 'Points',
            type: 'num-fmt'
        }
    ]);
    
    players_table.prependHeaderRow('\
        <th colspan="3">&nbsp;</th>\
        <th colspan="2">Score</th>\
        <th colspan="2">Speed</th>\
        <th colspan="2">Deathless</th>\
    ');
    
    players_table.setDataProcessCallback(window, 'process_power_data');
    
    players_table.render();
});