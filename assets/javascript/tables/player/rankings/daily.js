function process_ranking_daily_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                processed_data.push([
                    row_data.date,
                    row_data.rank,
                    row_data.first_place_ranks,
                    row_data.top_5_ranks,
                    row_data.top_10_ranks,
                    row_data.top_20_ranks,
                    row_data.top_50_ranks,
                    row_data.top_100_ranks,
                    row_data.total_score,
                    Formatting.roundNumber(row_data.total_points),
                    Formatting.roundNumber(row_data.points_per_day),
                    row_data.total_dailies,
                    row_data.total_wins,
                    Formatting.roundNumber(row_data.average_rank),
                    row_data.sum_of_ranks
                ]);
            }
        }
    }
    
    return processed_data;
};

function initialize_ranking_daily_table() {
    var table = new NecroTable($('#player_daily_rankings_table'));
    
    table.enableButtons();
    table.enablePaging();
    table.enableReleaseField();
    table.enableModeField();
    table.enableDateRangeFields();
    table.enableNumberOfDaysField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/daily/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'first_place_ranks',
            title: '1st<br />Place',
            type: 'num-fmt'
        },
        {
            name: 'top_5_ranks',
            title: 'Top<br />5',
            type: 'num-fmt'
        },
        {
            name: 'top_10_ranks',
            title: 'Top<br />10',
            type: 'num-fmt'
        },
        {
            name: 'top_20_ranks',
            title: 'Top<br />20',
            type: 'num-fmt'
        },
        {
            name: 'top_50_ranks',
            title: 'Top<br />50',
            type: 'num-fmt'
        },
        {
            name: 'top_100_ranks',
            title: 'Top<br />100',
            type: 'num-fmt'
        },
        {
            name: 'total_score',
            title: 'Total<br />Score',
            type: 'num-fmt'
        },
        {
            name: 'total_points',
            title: 'Points',
            type: 'num-fmt'
        },
        {
            name: 'points_per_day',
            title: 'Points<br />Per<br />Day',
            type: 'num-fmt'
        },
        {
            name: 'total_dailies',
            title: 'Attempts',
            type: 'num-fmt'
        },
        {
            name: 'total_wins',
            title: 'Wins',
            type: 'num-fmt'
        },
        {
            name: 'average_rank',
            title: 'Average<br />Rank',
            type: 'num-fmt'
        }
    ]);
    
    table.enableSort('date', 'desc');
    
    table.setDataProcessCallback(window, 'process_ranking_daily_data');
    
    table.render();
}; 
