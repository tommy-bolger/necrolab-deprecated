function EntriesTable(dom_object, configuration) {
    if(configuration == null) {
        configuration = {};
    }
    
    var instance = this;
    
    this.dom_object = dom_object;
    this.datatable;
    this.datatable_columns = [];
    this.raw_data = [];
    this.processed_data = [];
    this.raw_summary_row = {};
    this.processed_summary_row = {};
    this.render_finish_callback = {};
    this.table_rendered = false;
    this.data_rendered = false;
    
    this.page = 1;
    this.sort_by;
    this.sort_direction;
    this.rows_per_page;
    this.search;
    
    
    if(configuration['name'] == null) {
        throw "name is required in the configuration.";
    }
    
    this.name = configuration.name;
    
    this.url;
    
    if(configuration['url'] == null) {
        throw "url is required in the configuration.";
    }
    
    this.setUrl(configuration.url);

    this.columns = [];

    if(configuration.hasOwnProperty('columns') &&  configuration.columns.constructor === Array) {
        this.addColumns(configuration.columns);
    }
};

EntriesTable.prototype.getName = function() {
    return this.name;
};

EntriesTable.prototype.setUrl = function(url) {
    this.url = url;
};

EntriesTable.prototype.addColumn = function(column) {  
    if(typeof column !== 'object') {
        alert('addColumn() must be given an object of options.');
        
        return false;
    }
    
    if(!column.hasOwnProperty('name')) {
        alert('Columns passed into addColumn() must have a name property.');
        
        return false;
    }
    
    this.columns.push(column);
};

EntriesTable.prototype.addColumns = function(columns) {  
    var number_of_columns = columns.length;
        
    for(var index = 0; index < number_of_columns; index++) {
        this.addColumn(columns[index]);
    }
};

EntriesTable.prototype.setRenderFinishCallback = function(context, method) {
    this.render_finish_callback.context = context;
    this.render_finish_callback.method = method;
};

EntriesTable.prototype.generateDatatableColumns = function() {
    this.datatable_columns = [];
    
    var number_of_columns = this.columns.length;
    
    for(var column_index = 0; column_index < number_of_columns; column_index++) {
        var column = this.columns[column_index];
        
        if(column.hasOwnProperty('datatable')) {
            column.datatable.data = column.name;
            
            this.datatable_columns.push(column.datatable);
        }
    }
};

EntriesTable.prototype.getArrayFromObject = function(object) {
    var array = Object.keys(object).map(function(key) {
        return object[key];
    });
    
    return array;
};

EntriesTable.prototype.getRawData = function() {
    return this.raw_data;
};

EntriesTable.prototype.getProcessedData = function() {
    return this.processed_data;
};

EntriesTable.prototype.getRawSummaryRow = function() {
    return this.raw_summary_row;
};

EntriesTable.prototype.getSummaryRow = function() {
    return this.summary_row;
};

EntriesTable.prototype.render = function() {
    this.generateDatatableColumns();
    
    var instance = this;

    this.datatable = this.dom_object.DataTable({
        columns: instance.datatable_columns,
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
        pageLength: 100,
        lengthMenu: [
            100,
            500,
            1000
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
        /*footerCallback: function(tfoot_element, data, start, end, display) {
            if($(this).children('tfoot').length == 0) {
                var api = this.api();
                var table = api.table();
                var header = api.table().header();
                var header_column_count = $(header).children().children().length;
                
                var summary_row = instance.getArrayFromObject(instance.summary_row);
                
                var footer_column_html = '';
                
                for(var index = 0; index < header_column_count; index++) {
                    if(summary_row[index] != null) {
                        footer_column_html += '<td>' + summary_row[index] + '</td>';
                    }
                    else {
                        footer_column_html += '<td></td>';
                    }
                }
                
                $(this).append('<tfoot><tr>' + footer_column_html + '</tr></tfoot>');
            }
        }*/
    });
    
    console.log(this.dom_object);
    
    this.dom_object.on('order.dt', function(event, settings) {
        event.preventDefault();
        
        if(instance.data_rendered) {
            var order = instance.datatable.order();
            instance.clearTable();
            
            instance.sort_by = instance.columns[order[0][0]].name;
            instance.sort_direction = order[0][1];
            
            instance.run();
        }
    });
    
    this.dom_object.on('page.dt', function(event, settings) {
        event.preventDefault();
        
        if(instance.data_rendered) {
            var page = instance.datatable.page();
            console.log(page);
            
            //instance.clearTable();
            
            //instance.sort_by = instance.columns[order[0][0]].name;
            //instance.sort_direction = order[0][1];
            
            //instance.run();
        }
    });
};

EntriesTable.prototype.clearTable = function() {
    this.data_rendered = false;
    this.datatable.clear().draw();
};

EntriesTable.prototype.run = function() {
    var request = {};
    
    request.page = this.page;
    
    if(this.sort_by != null) {
        request.sort_by = this.sort_by;
    }
    
    if(this.sort_direction != null) {
        request.sort_direction = this.sort_direction;
    }
    
    if(this.rows_per_page != null) {
        request.rows_per_page = this.rows_per_page;
    }
    
    if(this.search != null) {
        request.search = this.search;
    }
    
    var instance = this;
    
    Request.get('/api/players/', request, {
        context: instance,
        method: 'process'
    });
};

EntriesTable.prototype.process = function(request, response) {
    this.raw_data = response.data;
    
    var raw_data_length = this.raw_data.length;
    var number_of_columns = this.columns.length;

    for(var raw_data_index = 0; raw_data_index < raw_data_length; raw_data_index++) {
        var raw_data_row = this.raw_data[raw_data_index];
                    
        var processed_row_data = {};
            
        for(var column_index = 0; column_index < number_of_columns; column_index++) {
            var column = this.columns[column_index];

            var column_value;
            
            if(column['callback'] != null) {
                column_value = column.callback(raw_data_row, this);
            }
            else if(column['value'] != null) {
                column_value = column.value;
            }
            else {
                column_value = raw_data_row[column.name];
            }
            
            if(!isNaN(column_value)) {
                this.raw_summary_row[column.name] = column_value;
            }
            
            if(column['format'] != null) {
                column_value = column.format(column_value);
            }
            
            if(column_value == null) {
                column_value = '&nbsp;';
            }
            
            processed_row_data[column.name] = column_value;
        }
        
        this.processed_data.push(processed_row_data);
    }

    //Loop through each column of the raw data summary row and create the finalized summary row           
    for(var column_index = 0; column_index < number_of_columns; column_index++) {
        var column = this.columns[column_index]

        if(column_index == 0) {
            this.raw_summary_row[column.name] = 'Total';
        }
        else {
            var value;
            
            if(column['callback'] != null) {
                value = column.callback(this.raw_summary_row, this);
            }
            else if(column['value'] != null) {
                value = column.value;
            }
            else {
                value = this.raw_summary_row[column.name];
            }
            
            if(column['format'] != null) {
                value = column.format(value);
            }
            
            if(value == null) {
                value = '&nbsp;';
            }
            
            this.processed_summary_row[column.name] = value;
        }
        
        if(column['query'] != null) {
            query_column_index += 1;
        }
    }
    
    this.datatable.clear();
    this.datatable.rows.add(this.processed_data);
    this.datatable.draw();
    
    this.data_rendered = true;
    
    if(this.render_finish_callback['context'] != null) {
        this.render_finish_callback.context[this.render_finish_callback.method](instance);
    }
};