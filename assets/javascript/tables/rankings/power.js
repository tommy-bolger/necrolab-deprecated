function process_power_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    null,
                    row_data.rank,
                    Formatting.getSocialMedia(row_data.player.steamid, row_data.player.linked),
                    Formatting.getNecrolabUserLink(row_data.player.steamid, row_data.player.personaname),
                    'Ranks',
                    row_data.score.rank,
                    row_data.speed.rank,
                    row_data.deathless.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    null,
                    'Points',
                    Formatting.roundNumber(row_data.score.rank_points),
                    Formatting.roundNumber(row_data.speed.rank_points),
                    Formatting.roundNumber(row_data.deathless.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    null,
                    'Score/Time/Wins',
                    row_data.score.total_score,
                    Formatting.convertSecondsToTime(row_data.speed.total_time),
                    row_data.deathless.total_win_count
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
    table.enableReleaseField();
    table.enableModeField();
    table.enableDateField();
    table.enableSiteField();
    table.enableSeededField();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/rankings/power/entries'));
    
    table.addColumns([
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
            name: 'type',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'score_rank',
            title: 'Score',
            type: 'num-fmt'
        },
        {
            name: 'speed_rank',
            title: 'Speed',
            type: 'num-fmt'
        },
        {
            name: 'deathless_rank',
            title: 'Deathless',
            type: 'num-fmt'
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_power_data');
    
    table.render();
});