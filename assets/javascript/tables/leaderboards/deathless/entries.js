function set_table_title(request, response) {    
    if(response['data'] != null) {
        $('#table_title').html(Formatting.getLeaderboardEntriesTitle(response.data));
    }
}

function process_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var run_result = null;
                
                if(row_data.win != 1) {
                    run_result = row_data.replay.run_result;
                }
                
                var processed_row = [
                    row_data.rank,
                    Formatting.getSocialMedia(row_data.player.steamid, row_data.player.linked),
                    Formatting.getNecrolabUserLink(row_data.player.steamid, row_data.player.personaname),
                    row_data.win_count,
                    row_data.zone,
                    row_data.level,
                    row_data.win,
                    run_result
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

$(document).ready(function() {    
    var table = new NecroTable($('#entries_table'));

    table.addRequestParameter('lbid');
    table.enableFixedHeader();
    table.enableButtons();
    table.enablePaging();
    table.enableHistory();
    table.enableSearchField();
    table.enableDateField();
    table.enableSiteField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/leaderboards/entries'));

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
            sortable: false
        },
        {
            name: 'run_result',
            title: 'Killed By',
            type: 'string'
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_data');
    
    Request.get(Formatting.getNecrolabApiUrl('/leaderboards/leaderboard'), {
        lbid: table.getUrl().getValue('lbid')
    }, {
        context: window,
        method: 'set_table_title'
    }, true);
    
    table.render();
});