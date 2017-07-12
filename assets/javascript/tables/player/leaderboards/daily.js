function process_leaderboard_daily_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var run_result = null;
                
                if(row_data.entry.win != 1) {
                    run_result = row_data.entry.replay.run_result;
                }
                
                var processed_row = [
                    row_data.date,
                    row_data.entry.rank,
                    row_data.entry.score,
                    row_data.entry.zone,
                    row_data.entry.level,
                    row_data.entry.win,
                    run_result,
                    row_data.entry.replay.seed,
                    Formatting.getReplayFileHtml(row_data.entry.replay.file_url)
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_leaderboard_daily_table() {
    var table = new NecroTable($('#player_daily_leaderboards_table'));

    table.enableButtons();
    table.enablePaging();
    table.enableReleaseField();
    table.enableModeField();
    table.enableDateRangeFields();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/leaderboards/daily/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'daily_date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'score',
            title: 'Score',
            type: 'num-fmt'
        },
        {
            name: 'zone',
            title: 'Zone',
            type: 'num-fmt'
        },
        {
            name: 'level',
            title: 'Level',
            type: 'num-fmt'
        },
        {
            name: 'win',
            title: 'Win',
            type: 'string',
            orderable: false
        },
        {
            name: 'run_result',
            title: 'Killed By',
            type: 'string'
        },
        {
            name: 'seed',
            title: 'Seed',
            type: 'string'
        },
        {
            name: 'replay',
            title: 'Replay',
            type: 'string',
            orderable: false
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_leaderboard_daily_data');
    
    table.render();
}; 
