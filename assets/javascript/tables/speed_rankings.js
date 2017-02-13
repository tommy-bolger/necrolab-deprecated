function process_speed_data(data, players_table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    Formatting.getNecrolabUserLink(row_data.player.steamid, row_data.player.personaname),
                    Formatting.getSocialMedia(row_data.player.steamid, row_data.player.linked),
                    row_data.speed.rank,
                    row_data.cadence.speed.rank,
                    row_data.bard.speed.rank,
                    row_data.monk.speed.rank,
                    row_data.aria.speed.rank,
                    row_data.bolt.speed.rank,
                    row_data.dove.speed.rank,
                    row_data.eli.speed.rank,
                    row_data.melody.speed.rank,
                    row_data.dorian.speed.rank,
                    row_data.coda.speed.rank,
                    row_data.nocturna.speed.rank,
                    row_data.all.speed.rank,
                    row_data.story.speed.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    '&nbsp;',
                    Formatting.roundNumber(row_data.speed.rank_points),
                    Formatting.roundNumber(row_data.cadence.speed.rank_points),
                    Formatting.roundNumber(row_data.bard.speed.rank_points),
                    Formatting.roundNumber(row_data.monk.speed.rank_points),
                    Formatting.roundNumber(row_data.aria.speed.rank_points),
                    Formatting.roundNumber(row_data.bolt.speed.rank_points),
                    Formatting.roundNumber(row_data.dove.speed.rank_points),
                    Formatting.roundNumber(row_data.eli.speed.rank_points),
                    Formatting.roundNumber(row_data.melody.speed.rank_points),
                    Formatting.roundNumber(row_data.dorian.speed.rank_points),
                    Formatting.roundNumber(row_data.coda.speed.rank_points),
                    Formatting.roundNumber(row_data.nocturna.speed.rank_points),
                    Formatting.roundNumber(row_data.all.speed.rank_points),
                    Formatting.roundNumber(row_data.story.speed.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    '&nbsp;',
                    row_data.speed.total_time,
                    row_data.cadence.speed.time,
                    row_data.bard.speed.time,
                    row_data.monk.speed.time,
                    row_data.aria.speed.time,
                    row_data.bolt.speed.time,
                    row_data.dove.speed.time,
                    row_data.eli.speed.time,
                    row_data.melody.speed.time,
                    row_data.dorian.speed.time,
                    row_data.coda.speed.time,
                    row_data.nocturna.speed.time,
                    row_data.all.speed.time,
                    row_data.story.speed.time
                ]);
            }
        }
    }
    
    return processed_data;
};

$(document).ready(function() {
    var players_table = new NecroTable($('#entries_table'));
    
    players_table.enableLengthMenu();
    players_table.enableButtons();
    players_table.enablePaging();
    players_table.enableSearchField();
    players_table.enableReleaseField();
    players_table.enableDateField();
    players_table.enableSiteField();
    
    players_table.setAjaxUrl('/api/rankings/power/speed/entries');
    
    players_table.addColumns([
        {
            name: 'personaname',
            title: 'Player',
            type: 'string'
        },
        {
            name: 'social_media',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'speed_rank',
            title: 'Overall',
            type: 'num-fmt'
        },
        {
            name: 'cadence_speed_rank',
            title: Formatting.getCharacterImageHtml('cadence'),
            type: 'num-fmt'
        },
        {
            name: 'bard_speed_rank',
            title: Formatting.getCharacterImageHtml('bard'),
            type: 'num-fmt'
        },
        {
            name: 'monk_speed_rank',
            title: Formatting.getCharacterImageHtml('monk'),
            type: 'num-fmt'
        },
        {
            name: 'aria_speed_rank',
            title: Formatting.getCharacterImageHtml('aria'),
            type: 'num-fmt'
        },
        {
            name: 'bolt_speed_rank',
            title: Formatting.getCharacterImageHtml('bolt'),
            type: 'num-fmt'
        },
        {
            name: 'dove_speed_rank',
            title: Formatting.getCharacterImageHtml('dove'),
            type: 'num-fmt'
        },
        {
            name: 'eli_speed_rank',
            title: Formatting.getCharacterImageHtml('eli'),
            type: 'num-fmt'
        },
        {
            name: 'melody_speed_rank',
            title: Formatting.getCharacterImageHtml('melody'),
            type: 'num-fmt'
        },
        {
            name: 'dorian_speed_rank',
            title: Formatting.getCharacterImageHtml('dorian'),
            type: 'num-fmt'
        },
        {
            name: 'coda_speed_rank',
            title: Formatting.getCharacterImageHtml('coda'),
            type: 'num-fmt'
        },
        {
            name: 'nocturna_speed_rank',
            title: Formatting.getCharacterImageHtml('nocturna'),
            type: 'num-fmt'
        },
        {
            name: 'all_speed_rank',
            title: 'All',
            type: 'num-fmt'
        },
        {
            name: 'story_speed_rank',
            title: 'Story',
            type: 'num-fmt'
        }
    ]);
    
    players_table.setDefaultSort('speed_rank', 'asc');
    
    players_table.setDataProcessCallback(window, 'process_speed_data');
    
    players_table.render();
});