function process_leaderboard_deathless_data(data, table) {
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
                    row_data.entry.win_count,
                    row_data.entry.zone,
                    row_data.entry.level,
                    row_data.entry.win,
                    run_result
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_leaderboard_deathless_table() {
    var table = new NecroTable($('#player_deathless_leaderboards_table'));

    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableSort('leaderboard_name', 'asc');
    table.enableReleaseField();
    table.enableDateField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/leaderboards/deathless/entries'));
    
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
            name: 'win_count',
            title: 'Wins',
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
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_leaderboard_deathless_data');
    
    table.render();
}; 
