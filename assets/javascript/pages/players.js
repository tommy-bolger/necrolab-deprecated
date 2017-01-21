$(document).ready(function() {
    var datatable = $('#entries_table').DataTable({
        dom: "<'col-sm-7'p><'row'<'col-sm-6'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        columns: [
            {
                name: 'personaname',
                title: 'Player',
                type: 'string'
            },
            {
                name: 'steamid',
                title: 'Steam',
                type: 'string'
            },
            {
                name: 'twitch_username',
                title: 'Twitch',
                type: 'string'
            },
            {
                name: 'twitter_username',
                title: 'Twitter',
                type: 'string'
            },
            {
                name: 'hitbox_username',
                title: 'Hitbox',
                type: 'string'
            },
            {
                name: 'nico_nico_url',
                title: '<span class="no_wrap">Nico Nico</span>',
                type: 'string'
            },
            {
                name: 'website',
                title: 'Website',
                type: 'string'
            }
        ],
        stateSave: false,
        fixedHeader: {
            header: false,
        },
        autoWidth: false,
        dom: 'Bfrtip',
        buttons: [
            'copy', 
            'csv', 
            'excel', 
            'pdf', 
            'print'
        ],
        paging: 'true',
        pagingType: 'simple',
        lengthMenu: [
            100,
            500,
            1000
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/players/',
            data: function(table_state) {
                return {
                    start: table_state.start,
                    sort_by: table_state.columns[table_state.order[0].column].name,
                    sort_direction: table_state.order[0].dir
                };
                console.log(table_state);
            },
            dataFilter: function(data) {
                var json = jQuery.parseJSON(data);
                json.recordsTotal = json.request.record_count;
                json.recordsFiltered = json.request.record_count;
    
                return JSON.stringify(json);
            },
            dataSrc: function(json) {
                var processed_data = [];
                
                if(json['data'] != null) {
                    var data_length = json.data.length;
                    
                    if(data_length > 0) {
                        
                        for(var index = 0; index < data_length; index++) {
                            var row_data = json.data[index];
                            
                            var processed_row = [];
                            
                            processed_row.push(Formatting.getNecrolabUserLink(row_data.steamid, row_data.personaname));
                            
                            processed_row.push(Formatting.getSteamProfileLink(row_data.steamid, row_data.personaname));
                            
                            processed_row.push(Formatting.getTwitchLink(row_data.twitch_username));
                            
                            processed_row.push(Formatting.getTwitterLink(row_data.twitter_username));
                            
                            processed_row.push(Formatting.getHitboxLink(row_data.hitbox_username));
                            
                            processed_row.push(Formatting.getNicoNicoLink(row_data.nico_nico_url));
                            
                            processed_row.push(Formatting.getWebsiteLink(row_data.website));
                            
                            processed_data.push(processed_row);
                        }
                    }
                }
                
                return processed_data;
            }
        },
        order: [
            [
                0,
                "asc"
            ]
        ],
        initComplete: function() {        
            var table_id = $(this).attr('id');
            
            if(table_id != null) {
                var search_input = $('div#' + table_id + '_filter input');
    
                search_input.unbind();
                
                var table = this;
    
                search_input.bind('keyup', function(e) {
                    if(e.keyCode == 13) {
                        table.api().search(this.value).draw();
                    }
                });
            }
        },
    });
});