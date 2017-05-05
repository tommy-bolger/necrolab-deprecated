function process_leaderboard_score_data(data, table) {
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
                    Formatting.getLeaderboardEntriesTitle(row_data.leaderboard),
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

function initialize_leaderboard_score_table() {
    var table = new NecroTable($('#player_score_leaderboards_table'));

    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableSort('leaderboard_name', 'asc');
    table.enableReleaseField();
    table.enableDateField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/leaderboards/score/entries'));
    
    table.addRequestParameter('id', 'steamid');

    table.addColumns([
        {
            name: 'leaderboard_name',
            title: 'Leaderboard',
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
    
    table.setDataProcessCallback(window, 'process_leaderboard_score_data');
    
    table.render();
}; 
