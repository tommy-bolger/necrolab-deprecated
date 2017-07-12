function process_pbs_score_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var run_result = null;
                
                if(row_data.pb.win != 1) {
                    run_result = row_data.pb.replay.run_result;
                }
                
                var processed_row = [
                    row_data.pb.date,
                    row_data.pb.rank,
                    row_data.pb.score,
                    row_data.pb.zone,
                    row_data.pb.level,
                    row_data.pb.win,
                    run_result,
                    row_data.pb.replay.seed,
                    Formatting.getReplayFileHtml(row_data.pb.replay.file_url)
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_pbs_score_table() {
    var table = new NecroTable($('#player_score_pbs_table'));

    table.enableButtons();
    table.enablePaging();
    table.enableReleaseField();
    table.enableModeField();
    table.enableSeededField();
    table.enableCoOpField();
    table.enableCustomField();
    table.enableCharacterField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/pbs/score'));
    
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
            type: 'string',
            orderable: false
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
    
    table.setDataProcessCallback(window, 'process_pbs_score_data');
    
    table.render();
}; 
