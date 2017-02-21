function process_character_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            var character_name = table.getCharacterFieldValue();
            
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var character_rankings = row_data[character_name];
                
                var rank_row = [
                    row_data.rank,
                    Formatting.getSocialMedia(row_data.player.steamid, row_data.player.linked),
                    Formatting.getNecrolabUserLink(row_data.player.steamid, row_data.player.personaname),
                    character_rankings.score.rank,
                    character_rankings.speed.rank,
                ];
                
                var points_row = [
                    null,
                    null,
                    null,
                    Formatting.roundNumber(character_rankings.score.rank_points),
                    Formatting.roundNumber(character_rankings.speed.rank_points)
                ];
                
                var score_row = [
                    null,
                    null,
                    null,
                    character_rankings.score.score,
                    Formatting.convertSecondsToTime(character_rankings.speed.time)
                ];
                
                switch(character_name) {
                    case 'all':
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

$(document).ready(function() {
    var table = new NecroTable($('#entries_table'));
    
    table.enableLengthMenu();
    table.enableButtons();
    table.enablePaging();
    table.enableSearchField();
    table.enableCharacterField();
    table.enableReleaseField();
    table.enableDateField();
    table.enableSiteField();
    
    table.setAjaxUrl('/api/rankings/power/character/entries');
    
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
    
    table.setDataProcessCallback(window, 'process_character_data');
    
    table.render();
});