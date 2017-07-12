function process_pbs_speed_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [
                    row_data.pb.date,
                    row_data.pb.rank,
                    Formatting.convertSecondsToTime(row_data.pb.time),
                    row_data.pb.replay.seed,
                    Formatting.getReplayFileHtml(row_data.pb.replay.file_url)
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_pbs_speed_table() {
    var table = new NecroTable($('#player_speed_pbs_table'));

    table.enableButtons();
    table.enablePaging();
    table.enableReleaseField();
    table.enableModeField();
    table.enableSeededField();
    table.enableCoOpField();
    table.enableCustomField();
    table.enableCharacterField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/pbs/speed'));
    
    table.addRequestParameter('id', 'steamid');

    table.addColumns([
        {
            name: 'snapshot_date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'first_rank',
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
    
    table.setDataProcessCallback(window, 'process_pbs_speed_data');
    
    table.render();
}; 
