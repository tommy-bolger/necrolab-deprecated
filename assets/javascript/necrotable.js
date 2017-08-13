function NecroTable(dom_object) {    
    var instance = this;
    
    this.dom_object = dom_object;
    this.table_id = dom_object.attr('id');
    this.ajax_url;
    this.datatable;
    this.dom_object_wrapper;
    this.columns = [];
    this.column_names = [];
    this.data_process_callback;
    this.url = new Url();
    this.init_requests = [];
    this.default_request = {};
    this.header_row_html = [];
    this.timezone = 'America/Los_Angeles';
    
    this.fixed_header = false;
    
    this.has_length_menu = false;
    this.length_menu = false;
    this.limit = 100;
    
    this.enable_buttons = false;
    this.buttons = [];
    
    this.paging = false;
    this.pagingType = false;
    this.start = 0;
    
    this.enable_history = false;
    this.state_changed = false;
    
    var current_date = moment().tz(this.timezone).format('YYYY-MM-DD');
    
    this.enable_date_field = false;
    this.date_field;
    this.date_field_value = current_date;
    
    this.enable_release_field = false;
    this.release_field;
    this.release_field_value = 'amplified_dlc';
    
    this.enable_mode_field = false;
    this.mode_field;
    this.mode_field_value = 'normal';
    
    this.enable_site_field = false;
    this.site_field;
    this.site_field_value = '';
    
    this.enable_character_field = false;
    this.character_field;
    this.character_field_value = 'cadence';
    
    this.enable_number_of_days_field = false;
    this.number_of_days_field;
    this.number_of_days_field_value = 30;
    
    this.enable_date_range_fields = false;
    this.start_date_field;
    this.start_date_field_value = current_date;
    this.end_date_field;
    this.end_date_field_value = current_date;
    
    this.enable_search_field = false;
    this.search_field_value = '';
    
    this.enable_seeded_field = false;
    this.seeded_field;
    this.seeded_field_value = 0;
    
    this.enable_co_op_field = false;
    this.co_op_field;
    this.co_op_field_value = 0;
    
    this.enable_custom_field = false;
    this.custom_field;
    this.custom_field_value = 0;
    
    this.enable_sort = false;
    this.sort_by;
    this.sort_direction = 'asc';
    this.sort_by_set_callback;
    this.sort_by_get_callback;
    
    this.enable_collapsible_rows = false;
    this.number_of_collapsible_rows = 1;
};

NecroTable.user_api_key;

NecroTable.current_date;

NecroTable.release_field_values = [];

NecroTable.prototype.getReleaseRecord = function() {      
    var release_values_length = NecroTable.release_field_values.length;
    
    var release_record = [];
    
    if(release_values_length > 0)  {
        for(var index = 0; index < release_values_length; index++) {
            var release_value = NecroTable.release_field_values[index];
            
            if(release_value.name == this.release_field_value) {
                release_record = release_value;
                
                break;
            }
        }
    }
    
    return release_record;
};

NecroTable.mode_field_values = [];

NecroTable.prototype.getModeRecord = function() {      
    var mode_values_length = NecroTable.mode_field_values.length;
    
    var mode_record = [];
    
    if(mode_values_length > 0)  {
        for(var index = 0; index < mode_values_length; index++) {
            var mode_value = NecroTable.mode_field_values[index];
            
            if(mode_value.name == this.mode_field_value) {
                mode_record = mode_value;
                
                break;
            }
        }
    }
    
    return mode_record;
};

NecroTable.site_field_values = [];

NecroTable.prototype.getSiteRecord = function() {      
    var site_values_length = NecroTable.site_field_values.length;
    
    var site_record = [];
    
    if(site_values_length > 0)  {
        for(var index = 0; index < site_values_length; index++) {
            var site_value = NecroTable.site_field_values[index];
            
            if(site_value.name == this.site_field_value) {
                site_record = site_value;
                
                break;
            }
        }
    }
    
    return site_record;
};

NecroTable.character_field_values = [];

NecroTable.prototype.getCharacterFieldValue = function() {      
    return this.character_field_value;
};

NecroTable.prototype.getCharacterRecord = function() {      
    var character_values_length = NecroTable.character_field_values.length;
    
    var character_record = [];
    
    if(character_values_length > 0)  {
        for(var index = 0; index < character_values_length; index++) {
            var character_value = NecroTable.character_field_values[index];
            
            if(character_value.name == this.character_field_value) {
                character_record = character_value;
                
                break;
            }
        }
    }
    
    return character_record;
};

NecroTable.number_of_days_field_values = [];

NecroTable.prototype.getUrl = function() {
    return this.url;
};

NecroTable.prototype.enableButtons = function() {  
    this.enable_buttons = true;
    this.buttons = [
        'copy', 
        'csv', 
        'excel', 
        'pdf', 
        'print'
    ];
};

NecroTable.prototype.enablePaging = function() {      
    this.paging = true;
    this.pagingType = 'full';
    
    this.setStartFromUrl();
};

NecroTable.prototype.setStartFromUrl = function() {  
    if(this.paging && this.enable_history) {
        var url_start = this.url.getValue('start');
        
        if(url_start != null) {
            this.start = parseInt(url_start);
        }
    }
};

NecroTable.prototype.enableSort = function(default_sort_by, default_sort_direction, set_callback, get_callback) {  
    this.enable_sort = true;
    this.sort_by_set_callback = set_callback;
    this.sort_by_get_callback = get_callback;

    this.setSortBy(default_sort_by);
    this.sort_direction = default_sort_direction;

    this.setSortFromUrl();
};

NecroTable.prototype.enableFixedHeader = function() {  
    this.fixed_header = true;
};

NecroTable.prototype.setSortBy = function(sort_by) {    
    if(this.sort_by_set_callback != null) {
        var callback_context = this.sort_by_set_callback.context;
        
        sort_by = callback_context[this.sort_by_set_callback.method](sort_by, this);
    }
    
    this.sort_by = sort_by;
};

NecroTable.prototype.getSortBy = function() { 
    var sort_by = this.sort_by;
    
    if(this.sort_by_get_callback != null) {
        var callback_context = this.sort_by_get_callback.context;
        
        sort_by = callback_context[this.sort_by_get_callback.method](sort_by, this);
    }
    
    return sort_by;
};

NecroTable.prototype.setSortFromUrl = function() {  
    if(this.enable_sort && this.enable_history) {
        var url_sort_by = this.url.getValue('sort_by');
        var url_sort_direction = this.url.getValue('sort_direction');

        if(url_sort_by != null && url_sort_direction != null) {
            this.setSortBy(url_sort_by);
            this.sort_direction = url_sort_direction;
        }
    }
};

NecroTable.prototype.enableHistory = function() {      
    this.enable_history = true;
};

NecroTable.prototype.enableDateField = function() {      
    this.enable_date_field = true;
    
    this.setDateFromUrl();
};

NecroTable.prototype.setDateFromUrl = function() {  
    if(this.enable_date_field && this.enable_history) {
        var url_date = this.url.getValue('date');
        
        if(url_date != null) {
            this.date_field_value = url_date;
        }
    }
};

NecroTable.prototype.enableDateRangeFields = function() {      
    this.enable_date_range_fields = true;
    
    this.setDateRangeFromUrl();
};

NecroTable.prototype.setDateRangeFromUrl = function() {  
    if(this.enable_date_range_fields && this.enable_history) {
        var url_start_date = this.url.getValue('start_date');
        
        if(url_start_date != null) {
            this.start_date_field_value = url_start_date;
        }
        
        var url_end_date = this.url.getValue('end_date');
        
        if(url_end_date != null) {
            this.end_date_field_value = url_end_date;
        }
    }
};

NecroTable.prototype.enableReleaseField = function() {      
    this.enable_release_field = true;
    
    this.setReleaseFromUrl();
};

NecroTable.prototype.setReleaseFromUrl = function() {  
    if(this.enable_release_field && this.enable_history) {
        var url_release = this.url.getValue('release');
        
        if(url_release != null) {
            this.release_field_value = url_release;
        }
    }
};

NecroTable.prototype.enableModeField = function() {      
    this.enable_mode_field = true;
    
    this.setModeFromUrl();
};

NecroTable.prototype.setModeFromUrl = function() {  
    if(this.enable_mode_field && this.enable_history) {
        var url_mode = this.url.getValue('mode');
        
        if(url_mode != null) {
            this.mode_field_value = url_mode;
        }
    }
};

NecroTable.prototype.enableSiteField = function() {      
    this.enable_site_field = true;
    
    this.setSiteFromUrl();
};

NecroTable.prototype.setSiteFromUrl = function() {  
    if(this.enable_site_field && this.enable_history) {
        var url_site = this.url.getValue('site');
        
        if(url_site != null) {
            this.site_field_value = url_site;
        }
    }
};

NecroTable.prototype.enableCharacterField = function() {      
    this.enable_character_field = true;
    
    this.setCharacterFromUrl();
};

NecroTable.prototype.setCharacterFromUrl = function() {  
    if(this.enable_character_field && this.enable_history) {
        var url_character = this.url.getValue('character');
        
        if(url_character != null) {
            this.character_field_value = url_character;
        }
    }
};

NecroTable.prototype.enableNumberOfDaysField = function() {      
    this.enable_number_of_days_field = true;
    
    this.setNumberOfDaysFromUrl();
};

NecroTable.prototype.setNumberOfDaysFromUrl = function() {  
    if(this.enable_number_of_days_field && this.enable_history) {
        var url_number_of_days = this.url.getValue('number_of_days');
        
        if(url_number_of_days != null) {
            this.number_of_days_field_value = url_number_of_days;
        }
    }
};

NecroTable.prototype.enableSearchField = function() {      
    this.enable_search_field = true;
    
    this.setSearchFromUrl();
};

NecroTable.prototype.setSearchFromUrl = function() {  
    if(this.enable_search_field && this.enable_history) {
        var url_search = this.url.getValue('search');
        
        if(url_search != null) {
            this.search_field_value = url_search;
        }
    }
};

NecroTable.prototype.enableSeededField = function() {      
    this.enable_seeded_field = true;
    
    this.setSeededFromUrl();
};

NecroTable.prototype.setSeededFromUrl = function() {  
    if(this.enable_seeded_field && this.enable_history) {
        var url_seeded = this.url.getValue('seeded');
        
        if(url_seeded != null) {
            this.seeded_field_value = url_seeded;
        }
    }
};

NecroTable.prototype.enableCoOpField = function() {      
    this.enable_co_op_field = true;
    
    this.setCoOpFromUrl();
};

NecroTable.prototype.setCoOpFromUrl = function() {  
    if(this.enable_co_op_field && this.enable_history) {
        var url_co_op = this.url.getValue('co_op');
        
        if(url_co_op != null) {
            this.co_op_field_value = url_co_op;
        }
    }
};

NecroTable.prototype.enableCustomField = function() {      
    this.enable_custom_field = true;
    
    this.setCustomFromUrl();
};

NecroTable.prototype.setCustomFromUrl = function() {  
    if(this.enable_custom_field && this.enable_history) {
        var url_custom = this.url.getValue('custom_music');
        
        if(url_custom != null) {
            this.custom_field_value = url_custom;
        }
    }
};

NecroTable.prototype.enableCollapsibleRows = function(number_of_collapsible_rows) {      
    this.enable_collapsible_rows = true;
    
    this.number_of_collapsible_rows = number_of_collapsible_rows;
    
    this.prependColumn({
        name: 'row_collapse_toggle',
        title: '&nbsp;',
        type: 'string',
        className: 'expand_control',
        orderable: false,
        data: 0,
        defaultContent: '<a class="expandable_row menu_small closed" href="#">+</a>'
    });
};

NecroTable.prototype.resumeFromHistoryState = function() {      
    this.url = new Url();    
    
    this.setStartFromUrl();
    
    this.setSortFromUrl();
    
    this.setDateFromUrl();
    
    this.setDateRangeFromUrl();
    
    this.setReleaseFromUrl();
    
    this.setModeFromUrl();
    
    this.setSiteFromUrl();
    
    this.setCharacterFromUrl();
    
    this.setNumberOfDaysFromUrl();
    
    this.setSearchFromUrl();
    
    this.setSeededFromUrl();
    
    this.setCoOpFromUrl();
    
    this.setCustomFromUrl();
    
    this.datatable.ajax.reload();
};

NecroTable.prototype.setAjaxUrl = function(ajax_url) {      
    this.ajax_url = ajax_url;
};

NecroTable.prototype.prependColumn = function(column) {      
    this.columns.unshift(column);
    this.column_names.unshift(column.name);
};

NecroTable.prototype.addColumn = function(column) {      
    this.columns.push(column);
    this.column_names.push(column.name);
};

NecroTable.prototype.addColumns = function(columns) {  
    var number_of_columns = columns.length;
        
    for(var index = 0; index < number_of_columns; index++) {
        this.addColumn(columns[index]);
    }
};

NecroTable.prototype.addRequestParameter = function(parameter_name, request_name) {      
    if(request_name == null) {
        request_name = parameter_name;
    }
    
    this.default_request[request_name] = this.url.getValue(parameter_name);
};

NecroTable.prototype.prependHeaderRow = function(header_row_html) {  
    this.header_row_html.unshift(header_row_html);
};

NecroTable.prototype.setDataProcessCallback = function(context, method) {      
    this.data_process_callback = {
        context: context,
        method: method
    };
};

NecroTable.prototype.removeInitRequest = function(init_request_name) {
    this.init_requests.splice(this.init_requests.indexOf(init_request_name), 1);
};

NecroTable.prototype.releaseRequestCallback = function(request, response) {      
    NecroTable.release_field_values = response.data;

    this.removeInitRequest('releases');
    
    this.render();
};

NecroTable.prototype.modeRequestCallback = function(request, response) {      
    NecroTable.mode_field_values = response.data;

    this.removeInitRequest('modes');
    
    this.render();
};

NecroTable.prototype.siteRequestCallback = function(request, response) {      
    NecroTable.site_field_values = response.data;
    
    NecroTable.site_field_values.unshift({
        name: '',
        display_name: 'Steam'
    });
    
    this.removeInitRequest('sites');
    
    this.render();
};

NecroTable.prototype.characterRequestCallback = function(request, response) {      
    NecroTable.character_field_values = response.data;
    
    this.removeInitRequest('characters');
    
    this.render();
};

NecroTable.prototype.numberOfDaysRequestCallback = function(request, response) {      
    NecroTable.number_of_days_field_values = response.data;
    
    this.removeInitRequest('number_of_days');
    
    this.render();
};

NecroTable.prototype.initInProgress = function() {      
    return (this.init_requests.length > 0);
};

NecroTable.prototype.initialize = function() {      
    if(this.initInProgress()) {        
        return false;
    }
    
    if(this.enable_release_field && NecroTable.release_field_values.length == 0) {
        this.init_requests.push('releases');
        
        Request.get(Formatting.getNecrolabApiUrl('/releases'), {}, {
            context: this,
            method: 'releaseRequestCallback'
        }, true);
    }
    
    if(this.enable_mode_field && NecroTable.mode_field_values.length == 0) {
        this.init_requests.push('modes');
        
        Request.get(Formatting.getNecrolabApiUrl('/modes'), {}, {
            context: this,
            method: 'modeRequestCallback'
        }, true);
    }
    
    if(this.enable_site_field && NecroTable.site_field_values.length == 0) {
        this.init_requests.push('sites');
        
        Request.get(Formatting.getNecrolabApiUrl('/external_sites'), {}, {
            context: this,
            method: 'siteRequestCallback'
        }, true);
    }
    
    if(this.enable_character_field && NecroTable.character_field_values.length == 0) {
        this.init_requests.push('characters');
        
        Request.get(Formatting.getNecrolabApiUrl('/characters'), {}, {
            context: this,
            method: 'characterRequestCallback'
        }, true);
    }
    
    if(this.enable_number_of_days_field && NecroTable.number_of_days_field_values.length == 0) {
        this.init_requests.push('number_of_days');
        
        Request.get(Formatting.getNecrolabApiUrl('/rankings/daily/number_of_days'), {}, {
            context: this,
            method: 'numberOfDaysRequestCallback'
        }, true);
    }
};

NecroTable.prototype.getDatepickerOptions = function() {
    var latest_date = moment().tz(this.timezone);
    var datepicker_options = {
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    };
    
    if(this.enable_release_field) {
        if(this.release_field_value.length > 0 && NecroTable.release_field_values.length > 0) {
            var release_record = this.getReleaseRecord();
            
            datepicker_options.startDate = moment(release_record.start_date).format('YYYY-MM-DD');
            
            var end_date;
            
            if(release_record['end_date'] != null) {
                end_date = moment(release_record.end_date, 'YYYY-MM-DD');
            }
            else {
                end_date = moment().tz(this.timezone);
            }
            
            if(end_date.isBefore(latest_date)) {
                latest_date = end_date;
            }
            
            datepicker_options.endDate = end_date.format('YYYY-MM-DD');
        }
    }
    
    datepicker_options.defaultViewDate = latest_date.format('YYYY-MM-DD');
    
    return datepicker_options;
};

NecroTable.prototype.initializeDateFieldPicker = function(destroy = false) {
    if(destroy) {
        this.date_field.unbind('changeDate');
        this.date_field.datepicker('destroy');
    }
    
    var datepicker_options = this.getDatepickerOptions();
    
    if(this.date_field_value != null) {
        var date_field_value = moment(this.date_field_value, 'YYYY-MM-DD');
        
        if(datepicker_options['startDate'] != null && datepicker_options['endDate'] != null) {
            var start_date = moment(datepicker_options.startDate, 'YYYY-MM-DD');
            var end_date = moment(datepicker_options.endDate, 'YYYY-MM-DD');
            
            if(!date_field_value.isBetween(start_date, end_date)) {
                date_field_value = moment(datepicker_options.defaultViewDate);
            }
        }
        
        this.date_field.val(date_field_value.format('YYYY-MM-DD'));
    }
    else {
        this.date_field.val(datepicker_options.defaultViewDate);
    }

    this.date_field.datepicker(datepicker_options);
    
    var instance = this;
    
    this.date_field.bind('changeDate', function(event) {
        instance.date_field_value = event.date;
        
        instance.datatable.ajax.reload();
    });
};

NecroTable.prototype.initializeStartDateFieldPicker = function(destroy = false) {
    if(destroy) {
        this.start_date_field.unbind('changeDate');
        this.start_date_field.datepicker('destroy');
    }
    
    var datepicker_options = this.getDatepickerOptions();    
    
    if(this.start_date_field_value != null) {
        var start_date_field_value = moment(this.start_date_field_value);
        
        if(datepicker_options['startDate'] != null && datepicker_options['endDate'] != null) {
            var start_date = moment(datepicker_options.startdate);
            var end_date = moment(datepicker_options.endDate);
            
            if(!start_date_field_value.isBetween(start_date, end_date)) {
                start_date_field_value = moment(datepicker_options.defaultViewDate);
            }
        }
        
        this.start_date_field.val(start_date_field_value.format('YYYY-MM-DD'));
    }
    else {
        this.start_date_field.val(datepicker_options.defaultViewDate);
    }
    
    this.start_date_field.datepicker(datepicker_options);
    
    var instance = this;
    
    this.start_date_field.bind('changeDate', function(event) {
        instance.start_date_field_value = event.date;
        
        instance.datatable.ajax.reload();
    });
};

NecroTable.prototype.initializeEndDateFieldPicker = function(destroy = false) {
    if(destroy) {
        this.end_date_field.unbind('changeDate');
        this.end_date_field.datepicker('destroy');
    }
    
    var datepicker_options = this.getDatepickerOptions();
    
    if(this.end_date_field_value != null) {
        var end_date_field_value = moment(this.end_date_field_value);
        
        if(datepicker_options['startDate'] != null && datepicker_options['endDate'] != null) {
            var start_date = moment(datepicker_options.startdate);
            var end_date = moment(datepicker_options.endDate);
            
            if(!end_date_field_value.isBetween(start_date, end_date)) {
                end_date_field_value = moment(datepicker_options.defaultViewDate);
            }
        }
        
        this.end_date_field.val(end_date_field_value.format('YYYY-MM-DD'));
    }
    else {
        this.end_date_field.val(datepicker_options.defaultViewDate);
    }
    
    this.end_date_field.datepicker(datepicker_options);
    
    var instance = this;
    
    this.end_date_field.bind('changeDate', function(event) {
        instance.end_date_field_value = event.date;
        
        instance.datatable.ajax.reload();
    });
};

NecroTable.prototype.render = function() {
    var instance = this;
    
    /* ---------- Make any requests for external data that populates menu items ---------- */
    
    instance.initialize();
    
    if(instance.initInProgress()) {
        return false;
    }
    
    /* ---------- Initialize sort by ---------- */
    
    var url_sort_by = instance.url.getValue('sort_by');
    
    if(url_sort_by != null) {
        instance.setSortBy(url_sort_by);
    }
    else {
        if(instance.sort_by == null) {            
            instance.setSortBy(instance.column_names[0]);
        }
    }
    
    var url_sort_direction = instance.url.getValue('sort_direction');
    
    if(url_sort_direction != null) {
        instance.sort_direction = url_sort_direction;
    }
    
    /* ---------- The dom string ---------- */
    
    var dom = '';
    
    var top_fields = '';
    
    if(instance.length_menu !== false) {
        top_fields += "<'col'l>";
    }
    
    if(instance.enable_search_field) {
        top_fields += "<'col'f>";
    }
    
    if(top_fields.length > 0) {
        dom += "<'row'" + top_fields + ">";
    }
    
    var custom_fields = '';
    
    if(instance.enable_character_field) {
        custom_fields += "<'col' <'#" + instance.table_id + "_character.top_menu_item'>>";
    }
    
    if(instance.enable_release_field) {
        custom_fields += "<'col' <'#" + instance.table_id + "_release.top_menu_item'>>";
    }
    
    if(instance.enable_mode_field) {
        custom_fields += "<'col' <'#" + instance.table_id + "_mode.top_menu_item'>>";
    }
    
    if(instance.enable_seeded_field) {
        custom_fields += "<'col' <'#" + instance.table_id + "_seeded.top_menu_item'>>";
    }
    
    if(instance.enable_co_op_field) {
        custom_fields += "<'col' <'#" + instance.table_id + "_co_op.top_menu_item'>>";
    }
    
    if(instance.enable_custom_field) {
        custom_fields += "<'col' <'#" + instance.table_id + "_custom.top_menu_item'>>";
    }
    
    if(instance.enable_site_field) {
        custom_fields += "<'col' <'#" + instance.table_id + "_site.top_menu_item'>>";
    }
    
    //TODO: Add a version field, maybe
    if(false) {
        custom_fields += "<'col' <'#" + instance.table_id + "_version.top_menu_item'>>";
    }
    
    if(instance.enable_number_of_days_field) {
        custom_fields += "<'col' <'#" + instance.table_id + "_number_of_days.top_menu_item'>>";
    }
    
    if(instance.enable_date_field) {
        custom_fields += "<'col' <'#" + instance.table_id + "_date.top_menu_item'>>";
    }
    
    if(instance.enable_date_range_fields) {
        custom_fields += "<'col' <'#" + instance.table_id + "_date_range.top_menu_item'>>";
    }
    
    if(custom_fields.length > 0) {
        dom += "<'row'" + custom_fields + ">";
    }
    
    if(instance.paging) {
        dom += "<'row'<'col top_info'i><br /><'clear'>>";
    }
    
    var last_top_bar = '';
    
    if(instance.enable_buttons) {
        last_top_bar += "<'col top_buttons'B>";
    }
    
    if(instance.paging) {
        last_top_bar += "<'col top_pagination'p><'clear'>";
    }
    
    if(last_top_bar.length > 0) {
        dom += "<'row'" + last_top_bar + ">";
    }
    
    dom += "<'row'<'col'tr>>";
    
    if(instance.paging) {
        dom += "<'row'<'col'i><'col'p>>";
    }
    
    /* ---------- Set datatable default sort ---------- */
        
    var default_order = [[
            0,
            'asc'
        ]];
    
    if(instance.enable_sort) {
        default_order = [[
            instance.column_names.indexOf(instance.sort_by),
            instance.sort_direction
        ]];
    }
    
    var row_callback; 
    var draw_callback;
    
    if(instance.enable_collapsible_rows) {
        row_callback = function(row, data, index) {
            if(index % (instance.number_of_collapsible_rows + 1) != 0) {
                $(row).hide();
            }
        }
        
        draw_callback = function(settings) {
            $('.expandable_row').click(function(event) {                    
                event.preventDefault();
                
                var table_rows = $('tr', instance.dom_object);
                
                var link_element = $(this);
                
                var current_row_index = link_element.parent().parent().index();
                var start_hidden_row_index = current_row_index + 2;
                var end_hidden_row_index = start_hidden_row_index + (instance.number_of_collapsible_rows - 1);
                
                for(var current_hidden_row_index = start_hidden_row_index; current_hidden_row_index <= end_hidden_row_index; current_hidden_row_index++) {
                    var table_row = table_rows.eq(current_hidden_row_index).toggle();
                }
                
                if(link_element.hasClass('closed')) {
                    link_element.removeClass('closed');
                    link_element.addClass('open');
                    
                    link_element.html('-');
                }
                else {
                    link_element.removeClass('open');
                    link_element.addClass('closed');
                    
                    link_element.html('+');
                }
            });
            
            $('#' + instance.table_id).popover({
                selector: '.icon_popover',
                animation: false,
                trigger: 'click',
            });
        };
    }    
    
    /* ---------- Render the actual datatable ---------- */

    instance.datatable = instance.dom_object.DataTable({
        dom: dom,
        columns: instance.columns,
        stateSave: false,
        fixedHeader: {
            header: instance.fixed_header,
        },
        responsive: false,
        autoWidth: false,
        buttons: instance.buttons,
        paging: instance.paging,
        pagingType: instance.pagingType,
        displayStart: instance.start,
        lengthMenu: instance.length_menu,
        pageLength: instance.limit,
        searching: instance.enable_search_field,
        ordering: instance.enable_sort,
        processing: true,
        serverSide: true,
        deferRender: true,
        ajax: {
            url: instance.ajax_url,
            dataType: "jsonp NecroTable",
            data: function(table_state) { 
                var request = instance.default_request;
                
                if(NecroTable.user_api_key != null) {
                    request.api_key = NecroTable.user_api_key;
                }
                
                if(instance.paging) {                    
                    instance.start = table_state.start;
                    request.start = instance.start;
                }
                
                /*if(instance.has_length_menu) {                    
                    instance.limit = table_state.length;
                }*/
                
                if(instance.enable_sort) {                    
                    instance.setSortBy(instance.column_names[table_state.order[0].column]);
                    
                    instance.sort_direction = table_state.order[0].dir;
                    
                    request.sort_by = instance.getSortBy();
                    request.sort_direction = instance.sort_direction;
                }
                
                if(instance.enable_release_field) {
                    request.release = instance.release_field_value;
                }
                
                if(instance.enable_mode_field) {
                    request.mode = instance.mode_field_value;
                }
                
                if(instance.enable_site_field) {
                    request.site = instance.site_field_value;
                }
                
                if(instance.enable_character_field) {
                    request.character = instance.character_field_value;
                }
                
                if(instance.enable_number_of_days_field) {
                    request.number_of_days = instance.number_of_days_field_value;
                }
                
                if(instance.enable_date_field) {
                    request.date = moment(instance.date_field_value).format('YYYY-MM-DD');
                }
                
                if(instance.enable_date_range_fields) {
                    request.start_date = moment(instance.start_date_field_value).format('YYYY-MM-DD');
                    request.end_date = moment(instance.end_date_field_value).format('YYYY-MM-DD');
                }
                
                if(instance.enable_search_field) {
                    request.search = instance.search_field_value;
                }
                
                if(instance.enable_seeded_field) {
                    request.seeded = instance.seeded_field_value;
                }
                
                if(instance.enable_co_op_field) {
                    request.co_op = instance.co_op_field_value;
                }
                
                if(instance.enable_custom_field) {
                    request.custom_music = instance.custom_field_value;
                }

                if(instance.enable_history) {
                    if(!this.state_changed) {
                        window.history.pushState(instance.url.getBaseUrl(), "NecroLab", Url.generateUrl(instance.url.getBaseUrl(), request));
                        
                        this.state_changed = true;
                    }
                    else {
                        window.history.replaceState(instance.url.getBaseUrl(), "NecroLab", Url.generateUrl(instance.url.getBaseUrl(), request));
                    }
                }
                
                return request;
            },
            converters: {
                'json NecroTable': function(data) {
                    data.recordsTotal = data.record_count;
                    data.recordsFiltered = data.record_count;
                    
                    return data;
                }
            },
            dataSrc: function(json) {
                if(instance.data_process_callback != null) {
                    var processed_data = [];
                
                    if(json['data'] != null) {
                        var callback_context = instance.data_process_callback.context;
                        
                        processed_data = callback_context[instance.data_process_callback.method](json.data, instance);
                    }
                    
                    return processed_data;
                }
                
                return json.data;
            }
        },
        order: default_order,
        rowCallback: row_callback,
        drawCallback: draw_callback,
        initComplete: function(settings, json) {            
            var table_id = $(this).attr('id');
            
            if(table_id != null) {
                var search_input = $('div#' + table_id + '_filter input');
                
                search_input.unbind();
                
                if(instance.search_field_value.length > 0) {
                    search_input.val(instance.search_field_value);
                }

                search_input.bind('keyup', function(e) {
                    if(e.keyCode == 13) {                        
                        instance.search_field_value = search_input.val();
                        
                        instance.datatable.ajax.reload();
                    }
                    else {
                        return false;
                    }
                });
            }
        },
    });
    
    $('#entries_table_length select').selectpicker({
        width: 'auto'
    });
    
    /* ---------- Set a processing overlay ---------- */
    
    /*
     instance.dom_object_wrapper = $('#' + instance.table_id + '_wrapper');
     
     instance.dom_object.on('processing.dt',function(e, settings, processing) {        
        if(processing) {
            instance.dom_object_wrapper.LoadingOverlay('show');
        }
        else {
            instance.dom_object_wrapper.LoadingOverlay('hide');
        }
    });*/
    
    /* ---------- Add a back button event to reload page state ---------- */
    
    if(instance.enable_history) {
        window.addEventListener("popstate", function(event) {            
            if(event.state == instance.url.getBaseUrl()) {
                instance.resumeFromHistoryState();
            }
            else {
                window.history.back();
            }
        });
    }
    
    /* ---------- Prepend any additional header rows ---------- */
    
    if(instance.header_row_html.length > 0) {
        var header_row_html = '<tr role="row">' + instance.header_row_html.join('</tr><tr role="row">') + '</tr>';
        
        $(instance.datatable.table().header()).prepend(header_row_html);
    }
    
    /* ---------- Render the release field if it's enabled ---------- */
    
    if(instance.enable_release_field) {
        var release_field_container = $('#' + instance.table_id + '_release');
        
        var release_field_html = '<select class="form-control input-sm release">';
        
        var release_values_length = NecroTable.release_field_values.length;
        
        for(var index = 0; index < release_values_length; index++) {
            var release_value = NecroTable.release_field_values[index];
            
            release_field_html += '<option value="' + release_value.name + '"';
            
            if(release_value.name == instance.release_field_value) {
                release_field_html += ' selected="selected"';
            }
            
            release_field_html += '>' + release_value.display_name + '</option>';
        }
        
        release_field_html += '</select>';
        
        release_field_container.html('\
            <label> \
            ' + release_field_html + ' \
            </label> \
        ');
        
        instance.release_field = release_field_container.children("label").children('select');

        instance.release_field.bind('change', function(event) {
            instance.release_field_value = $(this).val();
            
            if(instance.enable_date_field) {
                instance.initializeDateFieldPicker(true);
            }
            
            if(instance.enable_date_range_fields) {
                instance.initializeStartDateFieldPicker(true);
                instance.initializeEndDateFieldPicker(true);
            }
            
            instance.datatable.ajax.reload();
        });
        
        instance.release_field.selectpicker();
    }
    
    /* ---------- Render the mode field if it's enabled ---------- */
    
    if(instance.enable_mode_field) {
        var mode_field_container = $('#' + instance.table_id + '_mode');
        
        var mode_field_html = '<select class="form-control input-sm mode">';
        
        var mode_values_length = NecroTable.mode_field_values.length;
        
        for(var index = 0; index < mode_values_length; index++) {
            var mode_value = NecroTable.mode_field_values[index];
            
            mode_field_html += '<option value="' + mode_value.name + '"';
            
            if(mode_value.name == instance.mode_field_value) {
                mode_field_html += ' selected="selected"';
            }
            
            mode_field_html += '>' + mode_value.display_name + '</option>';
        }
        
        mode_field_html += '</select>';
        
        mode_field_container.html('\
            <label> \
            ' + mode_field_html + ' \
            </label> \
        ');
        
        instance.mode_field = mode_field_container.children("label").children('select');

        instance.mode_field.bind('change', function(event) {
            instance.mode_field_value = $(this).val();

            instance.datatable.ajax.reload();
        });
        
        instance.mode_field.selectpicker();
    }
    
    /* ---------- Render the site field if it's enabled ---------- */
    
    if(instance.enable_site_field) {
        var site_field_container = $('#' + instance.table_id + '_site');
        
        var site_field_html = '<select class="form-control input-sm site">';
        
        var site_values_length = NecroTable.site_field_values.length;
        
        for(var index = 0; index < site_values_length; index++) {
            var site_value = NecroTable.site_field_values[index];
            
            var site_icon_html = '';
            
            switch(site_value.name) {
                case 'beampro':
                    site_icon_html = Formatting.getBeamproLogo();
                    break;
                case 'discord':
                    site_icon_html = Formatting.getDiscordLogo();
                    break;
                case 'reddit':
                    site_icon_html = Formatting.getRedditLogo();
                    break;
                case 'twitch':
                    site_icon_html = Formatting.getTwitchLogo();
                    break;
                case 'twitter':
                    site_icon_html = Formatting.getTwitterLogo();
                    break;
                case 'youtube':
                    site_icon_html = Formatting.getYoutubeLogo();
                    break;
                default:
                    site_icon_html = Formatting.getSteamLogo();
                    break;
            }

            site_field_html += '<option value="' + site_value.name + '"';
            
            if(site_icon_html.length > 0) {
                site_icon_html = site_icon_html.replace(/"/g, '\'');
                
                site_field_html += ' data-content="' + site_icon_html + ' ' + site_value.display_name + '"';
            }
            
            if(site_value.name == instance.site_field_value) {
                site_field_html += ' selected="selected"';
            }
          
            site_field_html += '>' + site_value.display_name + '</option>';
        }
        
        site_field_html += '</select>';
        
        site_field_container.html('\
            <label> \
            ' + site_field_html + ' \
            </label> \
        ');
        
        instance.site_field = site_field_container.children("label").children('select');

        instance.site_field.bind('change', function(event) {
            instance.site_field_value = $(this).val();
            
            instance.datatable.ajax.reload();
        });
        
        instance.site_field.selectpicker();
    }
    
    /* ---------- Render the character field if it's enabled ---------- */
    
    if(instance.enable_character_field) {
        var character_field_container = $('#' + instance.table_id + '_character');
        
        var character_field_html = '<select class="form-control input-sm character">';
        
        var character_values_length = NecroTable.character_field_values.length;
        
        for(var index = 0; index < character_values_length; index++) {
            var character_value = NecroTable.character_field_values[index];
            
            var character_image_html;
            
            //A thing to note: bootstrap select's custom html requires that it have single quotes instead of double.
            if(character_value.name != 'all' && character_value.name != 'story' && character_value.name != 'all_dlc') {
                character_image_html = Formatting.getCharacterImageHtml(character_value.name) +  ' ' + character_value.display_name;
                character_image_html = character_image_html.replace(/"/g, '\'');
            }
            else if(character_value.name == 'all') {
                character_image_html = "<span class='menu_small'>" + character_value.display_name + "</span>";
            }
            else if(character_value.name == 'all_dlc') {
                character_image_html = "<span class='menu_small'>" + character_value.display_name + "</span>";
            }
            else if(character_value.name == 'story') {
                character_image_html = "<span class='menu_small'>Story</span>";
            }
            
            character_field_html += '<option data-content="' + character_image_html + '" value="' + character_value.name + '"';
            
            /*character_field_html += '<option data-content="<span class=\'character_header ' + character_value.name + '_header\'>' + 
                character_value.display_name + '</span>" value="' + character_value.name + '"';*/
            
            if(character_value.name == instance.character_field_value) {
                character_field_html += ' selected="selected"';
            }
          
            character_field_html += '>' + character_value.display_name + '</option>';
        }
        
        character_field_html += '</select>';
        
        character_field_container.html('\
            <label> \
            ' + character_field_html + ' \
            </label> \
        ');
        
        instance.character_field = character_field_container.children("label").children('select');

        instance.character_field.bind('change', function(event) {
            instance.character_field_value = $(this).val();
            
            instance.datatable.ajax.reload();
        });
        
        instance.character_field.selectpicker();
    }
    
    /* ---------- Render the number of days field if it's enabled ---------- */
    
    if(instance.enable_number_of_days_field) {
        var number_of_days_field_container = $('#' + instance.table_id + '_number_of_days');
        
        var number_of_days_field_html = '<select class="form-control input-sm number_of_days">';
        
        var number_of_days_values_length = NecroTable.number_of_days_field_values.length;
        
        for(var index = 0; index < number_of_days_values_length; index++) {
            var number_of_days_value = NecroTable.number_of_days_field_values[index];
            
            var display_name = number_of_days_value + ' Days';
            
            if(number_of_days_value == '0') {
                display_name = 'All';
            }
            
            number_of_days_field_html += '<option value="' + number_of_days_value + '"';
            
            if(number_of_days_value == instance.number_of_days_field_value) {
                number_of_days_field_html += ' selected="selected"';
            }
            
            number_of_days_field_html += '>' + display_name + '</option>';
        }
        
        number_of_days_field_html += '</select>';
        
        number_of_days_field_container.html('\
            <label> \
            ' + number_of_days_field_html + ' \
            </label> \
        ');
        
        instance.number_of_days_field = number_of_days_field_container.children("label").children('select');

        instance.number_of_days_field.bind('change', function(event) {
            instance.number_of_days_field_value = $(this).val();
            
            instance.datatable.ajax.reload();
        });
        
        instance.number_of_days_field.selectpicker();
    }
    
    /* ---------- Render the date field if it's enabled ---------- */

    if(instance.enable_date_field) {
        var date_field_container = $('#' + instance.table_id + '_date');
        
        date_field_container.html(
            '<div class="input-group date date_field"> \
                <input type="text" class="form-control input-sm" value="" placeholder="Date"> \
                <div class="input-group-addon"> \
                    <span class="glyphicon glyphicon-th"></span> \
                </div> \
            </div> \
        ');
        
        instance.date_field = date_field_container.children(".input-group").children('input');
        
        instance.initializeDateFieldPicker();
    }
    
    /* ---------- Render the date range fields if they're enabled ---------- */
    
    if(instance.enable_date_range_fields) {
        var date_field_container = $('#' + instance.table_id + '_date_range');
        
        date_field_container.html(' \
            <div class="input-group input-daterange"> \
                <input type="text" class="form-control input-sm start_date" value="" placeholder="Start Date"> \
                <div class="input-group-addon">to</div> \
                <input type="text" class="form-control input-sm end_date" value="" placeholder="End Date"> \
            </div> \
        ');
        
        instance.start_date_field = date_field_container.children(".input-group").children('input.start_date');
        
        instance.end_date_field = date_field_container.children(".input-group").children('input.end_date');
        
        instance.initializeStartDateFieldPicker();
        instance.initializeEndDateFieldPicker();
    }
    
    /* ---------- Render the seeded field if it's enabled ---------- */

    if(instance.enable_seeded_field) {
        var seeded_field_container = $('#' + instance.table_id + '_seeded');
        
        seeded_field_container.html('\
            <label> \
                <select class="form-control input-sm seeded"> \
                    <option value="0">Unseeded</option> \
                    <option value="1">Seeded</option> \
                </select> \
            </label> \
        ');
        
        instance.seeded_field = seeded_field_container.children("label").children('select');

        instance.seeded_field.bind('change', function(event) {
            instance.seeded_field_value = $(this).val();
            
            instance.datatable.ajax.reload();
        });
        
        instance.seeded_field.selectpicker();
    }
    
    /* ---------- Render the co-op field if it's enabled ---------- */

    if(instance.enable_co_op_field) {
        var co_op_field_container = $('#' + instance.table_id + '_co_op');
        
        co_op_field_container.html('\
            <label> \
                <select class="form-control input-sm co_op"> \
                    <option value="0">Single Player</option> \
                    <option value="1">Co-Op</option> \
                </select> \
            </label> \
        ');
        
        instance.co_op_field = co_op_field_container.children("label").children('select');

        instance.co_op_field.bind('change', function(event) {
            instance.co_op_field_value = $(this).val();
            
            instance.datatable.ajax.reload();
        });
        
        instance.co_op_field.selectpicker();
    }
    
    /* ---------- Render the custom field if it's enabled ---------- */

    if(instance.enable_custom_field) {
        var custom_field_container = $('#' + instance.table_id + '_custom');
        
        custom_field_container.html('\
            <label> \
                <select class="form-control input-sm custom"> \
                    <option value="0">Default Music</option> \
                    <option value="1">Custom Music</option> \
                </select> \
            </label> \
        ');
        
        instance.custom_field = custom_field_container.children("label").children('select');

        instance.custom_field.bind('change', function(event) {
            instance.custom_field_value = $(this).val();
            
            instance.datatable.ajax.reload();
        });
        
        instance.custom_field.selectpicker();
    }
};