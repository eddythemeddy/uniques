(function(){

    let _dom = $(document),
        _rangetable = $('#rangeTable');

    const _init = () => {
        _dataTables.init();
        _linkCalendar.init();
    }

    const _dataTables = {
        init: () => {
            $.fn.dataTable.ext.errMode = 'none';
            $.fn.dataTable.ext.classes.sPageButton = 'btn btn-xs';
            $.fn.dataTable.ext.classes.sFilter = 'data-table-filter';
            $.fn.dataTable.ext.classes.sPageButtonActive = 'btn-primary';
            $.fn.dataTable.ext.classes.sPagePrevious = 'flaticon flaticon-chevron-left no-border';
            $.fn.dataTable.ext.classes.sPageNext = 'flaticon flaticon-chevron-right no-border';
            _rangetable.DataTable(_dataTables.options);
        },
        options: {
            columns: [
                { data: "dateRange" },
                { data: "totalClients" },
                { data: "totalRecipes" }
            ],
            language: {
                paginate: {
                    previous: "<",
                    next: ">"
                },
                lengthMenu: "Per Page _MENU_",
            },
            dom: '<"top"fi>rt<"bottom"pl><"clear">',
            serverSide: true,
            processing: false,
            paging: true,
            pageLength: 10,
            ajax: {
                url: location.href,
                type: "POST",
                data: function ( d ) {
                    d.fetchRanges = "myValue";
                }
            },
            initComplete: function(settings, json) {
                
                $('<label class="pull-left m-r-10">Event Type<br/>' +
                    '<select class="filter-active">' +
                        '<option value="">All</option>' +
                        '<option value="public">Public</option>' +
                        '<option value="private">Private</option>' +
                    '</select>' + 
                  '</label><br/>').appendTo("#forecastTable_filter");
                $('h6.data-tables-header').html(json.iTotalRecords + ' weeks' + (json.iTotalRecords > 1 ? 's' : ''));
            }
        }
    }

    return {
        init: _init()
    };
})();