function process_score_data(data, table) {
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
                    row_data.score.rank,
                    row_data.cadence.score.rank,
                    row_data.bard.score.rank,
                    row_data.monk.score.rank,
                    row_data.aria.score.rank,
                    row_data.bolt.score.rank,
                    row_data.dove.score.rank,
                    row_data.eli.score.rank,
                    row_data.melody.score.rank,
                    row_data.dorian.score.rank,
                    row_data.coda.score.rank,
                    row_data.nocturna.score.rank,
                    row_data.diamond.score.rank,
                    row_data.mary.score.rank,
                    row_data.tempo.score.rank,
                    row_data.story.score.rank,
                    row_data.all.score.rank,
                    row_data.all_dlc.score.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Points',
                    Formatting.roundNumber(row_data.score.rank_points),
                    Formatting.roundNumber(row_data.cadence.score.rank_points),
                    Formatting.roundNumber(row_data.bard.score.rank_points),
                    Formatting.roundNumber(row_data.monk.score.rank_points),
                    Formatting.roundNumber(row_data.aria.score.rank_points),
                    Formatting.roundNumber(row_data.bolt.score.rank_points),
                    Formatting.roundNumber(row_data.dove.score.rank_points),
                    Formatting.roundNumber(row_data.eli.score.rank_points),
                    Formatting.roundNumber(row_data.melody.score.rank_points),
                    Formatting.roundNumber(row_data.dorian.score.rank_points),
                    Formatting.roundNumber(row_data.coda.score.rank_points),
                    Formatting.roundNumber(row_data.nocturna.score.rank_points),
                    Formatting.roundNumber(row_data.diamond.score.rank_points),
                    Formatting.roundNumber(row_data.mary.score.rank_points),
                    Formatting.roundNumber(row_data.tempo.score.rank_points),
                    Formatting.roundNumber(row_data.story.score.rank_points),
                    Formatting.roundNumber(row_data.all.score.rank_points),
                    Formatting.roundNumber(row_data.all_dlc.score.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Scores',
                    row_data.score.total_score,
                    row_data.cadence.score.score,
                    row_data.bard.score.score,
                    row_data.monk.score.score,
                    row_data.aria.score.score,
                    row_data.bolt.score.score,
                    row_data.dove.score.score,
                    row_data.eli.score.score,
                    row_data.melody.score.score,
                    row_data.dorian.score.score,
                    row_data.coda.score.score,
                    row_data.nocturna.score.score,
                    row_data.diamond.score.score,
                    row_data.mary.score.score,
                    row_data.tempo.score.score,
                    row_data.story.score.score,
                    row_data.all.score.score,
                    row_data.all_dlc.score.score
                ]);
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
    table.enableReleaseField();
    table.enableModeField();
    table.enableSeededField();
    table.enableDateField();
    table.enableSiteField();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/rankings/power/score/entries'));
    
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
            name: 'score_rank',
            title: 'Overall',
            type: 'num-fmt'
        },
        {
            name: 'cadence_score_rank',
            title: Formatting.getCharacterImageHtml('cadence'),
            type: 'num-fmt'
        },
        {
            name: 'bard_score_rank',
            title: Formatting.getCharacterImageHtml('bard'),
            type: 'num-fmt'
        },
        {
            name: 'monk_score_rank',
            title: Formatting.getCharacterImageHtml('monk'),
            type: 'num-fmt'
        },
        {
            name: 'aria_score_rank',
            title: Formatting.getCharacterImageHtml('aria'),
            type: 'num-fmt'
        },
        {
            name: 'bolt_score_rank',
            title: Formatting.getCharacterImageHtml('bolt'),
            type: 'num-fmt'
        },
        {
            name: 'dove_score_rank',
            title: Formatting.getCharacterImageHtml('dove'),
            type: 'num-fmt'
        },
        {
            name: 'eli_score_rank',
            title: Formatting.getCharacterImageHtml('eli'),
            type: 'num-fmt'
        },
        {
            name: 'melody_score_rank',
            title: Formatting.getCharacterImageHtml('melody'),
            type: 'num-fmt'
        },
        {
            name: 'dorian_score_rank',
            title: Formatting.getCharacterImageHtml('dorian'),
            type: 'num-fmt'
        },
        {
            name: 'coda_score_rank',
            title: Formatting.getCharacterImageHtml('coda'),
            type: 'num-fmt'
        },
        {
            name: 'nocturna_score_rank',
            title: Formatting.getCharacterImageHtml('nocturna'),
            type: 'num-fmt'
        },
        {
            name: 'diamond_score_rank',
            title: Formatting.getCharacterImageHtml('diamond'),
            type: 'num-fmt'
        },
        {
            name: 'mary_score_rank',
            title: Formatting.getCharacterImageHtml('mary'),
            type: 'num-fmt'
        },
        {
            name: 'tempo_score_rank',
            title: Formatting.getCharacterImageHtml('tempo'),
            type: 'num-fmt'
        },
        {
            name: 'story_score_rank',
            title: 'Story',
            type: 'num-fmt'
        },
        {
            name: 'all_score_rank',
            title: 'All',
            type: 'num-fmt'
        },
        {
            name: 'all_dlc_score_rank',
            title: 'All DLC',
            type: 'num-fmt'
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_score_data');
    
    table.render();
});