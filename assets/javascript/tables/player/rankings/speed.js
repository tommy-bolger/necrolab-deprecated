function process_ranking_speed_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    null,
                    row_data.date,
                    row_data.mode.display_name,
                    'Ranks',
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
                    row_data.diamond.speed.rank,
                    row_data.all.speed.rank,
                    row_data.story.speed.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Points',
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
                    Formatting.roundNumber(row_data.diamond.speed.rank_points),
                    Formatting.roundNumber(row_data.all.speed.rank_points),
                    Formatting.roundNumber(row_data.story.speed.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Times',
                    Formatting.convertSecondsToTime(row_data.speed.total_time),
                    Formatting.convertSecondsToTime(row_data.cadence.speed.time),
                    Formatting.convertSecondsToTime(row_data.bard.speed.time),
                    Formatting.convertSecondsToTime(row_data.monk.speed.time),
                    Formatting.convertSecondsToTime(row_data.aria.speed.time),
                    Formatting.convertSecondsToTime(row_data.bolt.speed.time),
                    Formatting.convertSecondsToTime(row_data.dove.speed.time),
                    Formatting.convertSecondsToTime(row_data.eli.speed.time),
                    Formatting.convertSecondsToTime(row_data.melody.speed.time),
                    Formatting.convertSecondsToTime(row_data.dorian.speed.time),
                    Formatting.convertSecondsToTime(row_data.coda.speed.time),
                    Formatting.convertSecondsToTime(row_data.nocturna.speed.time),
                    Formatting.convertSecondsToTime(row_data.diamond.speed.time),
                    Formatting.convertSecondsToTime(row_data.all.speed.time),
                    Formatting.convertSecondsToTime(row_data.story.speed.time)
                ]);
            }
        }
    }
    
    return processed_data;
};

function initialize_ranking_speed_table() {
    var table = new NecroTable($('#player_speed_rankings_table'));
    
    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableReleaseField();
    table.enableDateRangeFields();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/power/speed/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'mode',
            title: 'Mode',
            type: 'string',
            orderable: false
        },
        {
            name: 'type',
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
            name: 'diamond_speed_rank',
            title: Formatting.getCharacterImageHtml('diamond'),
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
    
    table.enableSort('date', 'desc');
    
    table.setDataProcessCallback(window, 'process_ranking_speed_data');
    
    table.render();
}; 
