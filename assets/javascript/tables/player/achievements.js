function process_achievements_data(data, table) {
    var data_length = data.length;
    var completed_achievements = 0;
    
    var processed_data = [];
    
    for(var index = 0; index < data_length; index++) {
        var data_row = data[index];
        
        processed_data.push([
            Formatting.getAchievementIconHtml(data_row.icon_url),
            data_row.display_name,
            data_row.description,
            Formatting.addNoWrapHtml(data_row.achieved_date)
        ]);
        
        if(data_row.achieved_date != null) {
            completed_achievements += 1;
        }
    }
    
    completed_achievements_percent = Math.round((completed_achievements / data_length) * 100);
    
    var progress_bar = $('#achievements_progress_bar');
    
    progress_bar.attr('aria-valuenow', completed_achievements_percent);
    progress_bar.css("width", completed_achievements_percent + '%');
    progress_bar.html(completed_achievements_percent + '%');
    
    if(completed_achievements == 0) {
        progress_bar.addClass('zero_progress');
    }
    
    return processed_data;
};

function initialize_achievements_table() {
    var table = new NecroTable($('#player_achievements_table'));
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/achievements'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'achieved',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'display_name',
            title: 'Achievement',
            type: 'string',
            orderable: false
        },
        {
            name: 'description',
            title: 'Description',
            type: 'string',
            orderable: false
        },
        {
            name: 'achieved_date',
            title: 'Achieved',
            type: 'string',
            orderable: false
        },
    ]);
    
    table.setDataProcessCallback(window, 'process_achievements_data');
    
    table.render();
}; 
