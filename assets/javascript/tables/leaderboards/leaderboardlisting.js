function LeaderboardListing(type) {
    this.type = type;
    
    this.table = new NecroTable($('#entries_table'));

    this.table.enableReleaseField();
    this.table.enableModeField();
    this.table.enableHistory();
    
    this.table.setAjaxUrl(Formatting.getNecrolabApiUrl('/leaderboards/' + this.type));
    
    var columns = [
        {
            name: 'name',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'cadence',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'bard',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'aria',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'bolt',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'monk',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'dove',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'eli',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'melody',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'dorian',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'coda',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'nocturna',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'diamond',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'mary',
            title: '&nbsp;',
            type: 'string'
        },
        {
            name: 'tempo',
            title: '&nbsp;',
            type: 'string'
        }
    ];
    
    if(this.type != 'deathless') {
        columns.push({
            name: 'story',
            title: '&nbsp;',
            type: 'string'
        });
        
        columns.push({
            name: 'all',
            title: '&nbsp;',
            type: 'string'
        });
        
        columns.push({
            name: 'all_dlc',
            title: '&nbsp;',
            type: 'string'
        });
    }
    
    this.table.addColumns(columns);
    
    this.table.setDataProcessCallback(this, 'process_data');
    
    this.table.render();
};

LeaderboardListing.prototype.process_data = function(data, players_table) {
    var processed_data = [];
    
    if(data.length > 0) {
        var data_length = data.length;
        
        if(data_length > 0) {
            var blank_row = [
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null
            ];
            
            if(this.type != 'deathless') {
                blank_row.push(null);
                blank_row.push(null);
                blank_row.push(null);
            }
            
            var all_zones_row = blank_row.slice(0);
            all_zones_row.splice(0, 1, 'All Zones Mode');
            
            var seeded_row = blank_row.slice(0);
            seeded_row.splice(0, 1, 'Seeded');
            
            var custom_music_row = blank_row.slice(0);
            custom_music_row.splice(0, 1, 'Custom Music');
            
            var seed_custom_music_row = blank_row.slice(0);
            seed_custom_music_row.splice(0, 1, 'Seeded Custom Music');
            
            var co_op_row = blank_row.slice(0);
            co_op_row.splice(0, 1, 'Co-Op');
            
            var seeded_co_op_row = blank_row.slice(0);
            seeded_co_op_row.splice(0, 1, 'Seeded Co-Op');
            
            var co_op_custom_music_row = blank_row.slice(0);
            co_op_custom_music_row.splice(0, 1, 'Co-Op Custom Music');
            
            var seeded_co_op_custom_music_row = blank_row.slice(0);
            seeded_co_op_custom_music_row.splice(0, 1, 'Seeded Co-Op Custom Music');
            
            var leaderboard_entries_url = '/leaderboards/' + this.type + '/entries';

            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var link_display = '';
                
                if(this.type != 'deathless' && row_data.character == 'all') {
                    link_display = 'All Chars';
                }
                else if(this.type != 'deathless' && row_data.character == 'all_dlc') {
                    link_display = 'All Chars DLC';
                }
                else if(this.type != 'deathless' && row_data.character == 'story') {
                    link_display = 'Story Mode';
                }
                else {
                    link_display = Formatting.getCharacterImageHtml(row_data.character);
                }
                
                var entries_link_html = '<a href="' + leaderboard_entries_url + '?lbid=' + row_data.lbid + '">' + link_display + '</a>';
                
                if(row_data.is_co_op == 0) {
                    if(row_data.is_seeded == 0) {
                        if(row_data.is_custom == 0) {
                            all_zones_row.splice(row_data.character_number, 1, entries_link_html);
                        }
                        else {
                            custom_music_row.splice(row_data.character_number, 1, entries_link_html);
                        }
                    }
                    else {
                        if(row_data.is_custom == 0) {
                            seeded_row.splice(row_data.character_number, 1, entries_link_html);
                        }
                        else {
                            seed_custom_music_row.splice(row_data.character_number, 1, entries_link_html);
                        }
                    }
                }
                else {
                    if(row_data.is_seeded == 0) {
                        if(row_data.is_custom == 0) {
                            co_op_row.splice(row_data.character_number, 1, entries_link_html);
                        }
                        else {
                            co_op_custom_music_row.splice(row_data.character_number, 1, entries_link_html);
                        }
                    }
                    else {
                        if(row_data.is_custom == 0) {
                            seeded_co_op_row.splice(row_data.character_number, 1, entries_link_html);
                        }
                        else {
                            seeded_co_op_custom_music_row.splice(row_data.character_number, 1, entries_link_html);
                        }
                    }
                }
            }
            
            if(this.type != 'deathless') {
                processed_data = [
                    all_zones_row,
                    seeded_row,
                    custom_music_row,
                    seed_custom_music_row,
                    co_op_row,
                    seeded_co_op_row,
                    co_op_custom_music_row,
                    seeded_co_op_custom_music_row
                ];
            }
            else {
                processed_data = [
                    all_zones_row,
                    custom_music_row,
                    co_op_row,
                    co_op_custom_music_row,
                ];
            }
        }
    }
    
    return processed_data;
};