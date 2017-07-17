function process_ranking_character_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            var character_name = table.getCharacterFieldValue();
            
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var character_rankings = row_data[character_name];
                
                var rank_row = [
                    null,
                    row_data.date,
                    'Ranks',
                    row_data.rank,
                    character_rankings.score.rank,
                    character_rankings.speed.rank,
                ];
                
                var points_row = [
                    '&nbsp;',
                    null,
                    'Points',
                    Formatting.roundNumber(character_rankings.rank_points),
                    Formatting.roundNumber(character_rankings.score.rank_points),
                    Formatting.roundNumber(character_rankings.speed.rank_points)
                ];
                
                var score_row = [
                    '&nbsp;',
                    null,
                    'Score/Time/Wins',
                    null,
                    character_rankings.score.score,
                    Formatting.convertSecondsToTime(character_rankings.speed.time)
                ];
                
                switch(character_name) {
                    case 'all':
                    case 'all_dlc':
                    case 'story':
                        rank_row.push(null);
                        points_row.push(null);
                        score_row.push(null);
                        break;
                    default:
                        rank_row.push(character_rankings.deathless.rank);
                        points_row.push(Formatting.roundNumber(character_rankings.deathless.rank_points));
                        score_row.push(character_rankings.deathless.win_count);
                        break;
                }
                
                processed_data.push(rank_row);
                processed_data.push(points_row);
                processed_data.push(score_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_ranking_character_table() {
    var table = new NecroTable($('#player_character_rankings_table'));
    
    table.enableButtons();
    table.enablePaging();
    table.enableCharacterField();
    table.enableReleaseField();
    table.enableModeField();
    table.enableSeededField();
    table.enableDateRangeFields();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/power/character/entries'));
    
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
    
    table.setDataProcessCallback(window, 'process_ranking_character_data');
    
    table.render();
}; 