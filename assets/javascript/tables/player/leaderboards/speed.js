function process_leaderboard_speed_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [
                    Formatting.getLeaderboardEntriesTitle(row_data.leaderboard),
                    row_data.entry.rank,
                    Formatting.convertSecondsToTime(row_data.entry.time),
                    row_data.entry.replay.seed,
                    Formatting.getReplayFileHtml(row_data.entry.replay.file_url)
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_leaderboard_speed_table() {
    var table = new NecroTable($('#player_speed_leaderboards_table'));

    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableSort('leaderboard_name', 'asc');
    table.enableReleaseField();
    table.enableDateField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/leaderboards/speed/entries'));
    
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
            name: 'time',
            title: 'Time',
            type: 'num-fmt'
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
    
    table.setDataProcessCallback(window, 'process_leaderboard_speed_data');
    
    table.render();
}; 
