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
                    row_data.date,
                    'Ranks',
                    row_data.rank,
                    row_data.score.rank,
                    row_data.speed.rank,
                    row_data.deathless.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    'Points',
                    Formatting.roundNumber(row_data.total_points),
                    Formatting.roundNumber(row_data.score.rank_points),
                    Formatting.roundNumber(row_data.speed.rank_points),
                    Formatting.roundNumber(row_data.deathless.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    'Score/Time/Wins',
                    null,
                    row_data.score.total_score,
                    Formatting.convertSecondsToTime(row_data.speed.total_time),
                    row_data.deathless.total_win_count
                ]);
            }
        }
    }
    
    return processed_data;
};

function initialize_power_table() {
    var table = new NecroTable($('#player_power_rankings_table'));

    table.enableButtons();
    table.enablePaging();
    table.enableReleaseField();
    table.enableModeField();
    table.enableSeededField();
    table.enableDateRangeFields();
    table.enableCollapsibleRows(2);    
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/power/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'type',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'rank',
            title: 'Overall',
            type: 'num-fmt'
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
}; 
