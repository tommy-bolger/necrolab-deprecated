function process_deathless_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    null,
                    Formatting.getNecrolabUserLink(row_data.player.steamid, row_data.player.personaname),
                    Formatting.getSocialMedia(row_data.player.steamid, row_data.player.linked),
                    'Ranks',
                    row_data.deathless.rank,
                    row_data.cadence.deathless.rank,
                    row_data.bard.deathless.rank,
                    row_data.monk.deathless.rank,
                    row_data.aria.deathless.rank,
                    row_data.bolt.deathless.rank,
                    row_data.dove.deathless.rank,
                    row_data.eli.deathless.rank,
                    row_data.melody.deathless.rank,
                    row_data.dorian.deathless.rank,
                    row_data.coda.deathless.rank,
                    row_data.nocturna.deathless.rank,
                    row_data.diamond.deathless.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Points',
                    Formatting.roundNumber(row_data.deathless.rank_points),
                    Formatting.roundNumber(row_data.cadence.deathless.rank_points),
                    Formatting.roundNumber(row_data.bard.deathless.rank_points),
                    Formatting.roundNumber(row_data.monk.deathless.rank_points),
                    Formatting.roundNumber(row_data.aria.deathless.rank_points),
                    Formatting.roundNumber(row_data.bolt.deathless.rank_points),
                    Formatting.roundNumber(row_data.dove.deathless.rank_points),
                    Formatting.roundNumber(row_data.eli.deathless.rank_points),
                    Formatting.roundNumber(row_data.melody.deathless.rank_points),
                    Formatting.roundNumber(row_data.dorian.deathless.rank_points),
                    Formatting.roundNumber(row_data.coda.deathless.rank_points),
                    Formatting.roundNumber(row_data.nocturna.deathless.rank_points),
                    Formatting.roundNumber(row_data.diamond.deathless.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Wins',
                    row_data.deathless.total_win_count,
                    row_data.cadence.deathless.win_count,
                    row_data.bard.deathless.win_count,
                    row_data.monk.deathless.win_count,
                    row_data.aria.deathless.win_count,
                    row_data.bolt.deathless.win_count,
                    row_data.dove.deathless.win_count,
                    row_data.eli.deathless.win_count,
                    row_data.melody.deathless.win_count,
                    row_data.dorian.deathless.win_count,
                    row_data.coda.deathless.win_count,
                    row_data.nocturna.deathless.win_count,
                    row_data.diamond.deathless.win_count
                ]);
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
    table.enableHistory();
    table.enableSearchField();
    table.enableReleaseField();
    table.enableModeField();
    table.enableDateField();
    table.enableSiteField();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/rankings/power/deathless/entries'));
    
    table.addColumns([
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
            name: 'type',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'deathless_rank',
            title: 'Overall',
            type: 'num-fmt'
        },
        {
            name: 'cadence_deathless_rank',
            title: Formatting.getCharacterImageHtml('cadence'),
            type: 'num-fmt'
        },
        {
            name: 'bard_deathless_rank',
            title: Formatting.getCharacterImageHtml('bard'),
            type: 'num-fmt'
        },
        {
            name: 'monk_deathless_rank',
            title: Formatting.getCharacterImageHtml('monk'),
            type: 'num-fmt'
        },
        {
            name: 'aria_deathless_rank',
            title: Formatting.getCharacterImageHtml('aria'),
            type: 'num-fmt'
        },
        {
            name: 'bolt_deathless_rank',
            title: Formatting.getCharacterImageHtml('bolt'),
            type: 'num-fmt'
        },
        {
            name: 'dove_deathless_rank',
            title: Formatting.getCharacterImageHtml('dove'),
            type: 'num-fmt'
        },
        {
            name: 'eli_deathless_rank',
            title: Formatting.getCharacterImageHtml('eli'),
            type: 'num-fmt'
        },
        {
            name: 'melody_deathless_rank',
            title: Formatting.getCharacterImageHtml('melody'),
            type: 'num-fmt'
        },
        {
            name: 'dorian_deathless_rank',
            title: Formatting.getCharacterImageHtml('dorian'),
            type: 'num-fmt'
        },
        {
            name: 'coda_deathless_rank',
            title: Formatting.getCharacterImageHtml('coda'),
            type: 'num-fmt'
        },
        {
            name: 'nocturna_deathless_rank',
            title: Formatting.getCharacterImageHtml('nocturna'),
            type: 'num-fmt'
        },
        {
            name: 'diamond_deathless_rank',
            title: Formatting.getCharacterImageHtml('diamond'),
            type: 'num-fmt'
        }
    ]);
    
    table.enableSort('deathless_rank', 'asc');
    
    table.setDataProcessCallback(window, 'process_deathless_data');
    
    table.render();
});