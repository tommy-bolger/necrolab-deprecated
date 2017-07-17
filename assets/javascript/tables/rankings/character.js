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
                    null,
                    character_rankings.rank,
                    Formatting.getSocialMedia(row_data.player.steamid, row_data.player.linked),
                    Formatting.getNecrolabUserLink(row_data.player.steamid, row_data.player.personaname),
                    'Ranks',
                    character_rankings.score.rank,
                    character_rankings.speed.rank,
                ];
                
                var points_row = [
                    '&nbsp;',
                    null,
                    null,
                    null,
                    'Points',
                    Formatting.roundNumber(character_rankings.score.rank_points),
                    Formatting.roundNumber(character_rankings.speed.rank_points)
                ];
                
                var score_row = [
                    '&nbsp;',
                    null,
                    null,
                    null,
                    'Score/Time/Wins',
                    character_rankings.score.score,
                    Formatting.convertSecondsToTime(character_rankings.speed.time)
                ];
                
                switch(character_name) {                    
                    case 'story':
                    case 'all':
                    case 'all_dlc':
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
    
    table.enableFixedHeader();
    table.enableButtons();
    table.enablePaging();
    table.enableHistory();    
    table.enableSearchField();
    table.enableCharacterField();
    table.enableReleaseField();
    table.enableModeField();
    table.enableDateField();
    table.enableSeededField();
    table.enableSiteField();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/rankings/power/character/entries'));
    
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
            name: 'type',
            title: '&nbsp;',
            type: 'string',
            orderable: false
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