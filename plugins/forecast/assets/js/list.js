(function() {

	var _ingredientTable = $('#forecastTable'),
		_dom = $(document),
		_tableObj = null;

	function _init(){
		_dataTables.init();
		_linkCalendar.init();
		_dom.on('change','.filter-active,.filter-state',function(){
			_tableObj.ajax.reload();
		})
	}

	_dataTables = {
		init: () => {
			$.fn.dataTable.ext.classes.sPageButton = 'btn btn-xs';
			$.fn.dataTable.ext.classes.sFilter = 'data-table-filter';
			$.fn.dataTable.ext.classes.sPageButtonActive = 'btn-primary';
			$.fn.dataTable.ext.classes.sPagePrevious = 'flaticon flaticon-chevron-left no-border';
			$.fn.dataTable.ext.classes.sPageNext = 'flaticon flaticon-chevron-right no-border';
			_tableObj = _ingredientTable.DataTable(_dataTables.options);
		},
		options: {
			order: [
				[ 1, 'desc' ]
			],
	        columns: [
	            { data: "eventIdPretty", class: "text-left v-align-middle" },
	            { data: "datePretty", class: "text-left v-align-middle"},
	            { data: "client", class: "text-center v-align-middle"},
	            { data: "eventType", class: "text-center v-align-middle"},
	            { data: "event_progress", class: "text-center v-align-middle"},
	            { data: "total_orders", class: "text-center v-align-middle", title: "# Orders"},
	            { data: "totalPrice", class: "text-right v-align-middle total-price"},
	            // { data: "statusPretty", class: "text-right v-align-middle"}
	        ],
	        stripeClasses: [ 'even', 'odd' ],
			language: {
				paginate: {
					previous: "<",
					next: ">"
				},
				lengthMenu: "Per Page _MENU_",
			},
			dom: '<"top"f>rt<"bottom"pli><"clear">',
		    serverSide: true,
		    searching: false,
		    processing: false,
		    paging: false,
			pageLength: 10,
	        ajax: {
	            url: location.href,
            	type: "POST",
	            data: function ( d, callback) {
		            	d.fetchForecast = 1;
		            	d.rangeView = 1;
		                d.event_type = $('.filter-active').val();
	            },
				dataSrc: function (json) {
					$.when(
						setTimeout(function(){
							_ingredientTable.find('tbody tr').each(function () {
					        var tr = $(this),
					        	row = _tableObj.row( tr ),
					        	rowData = row.data();

						        if(rowData != undefined && rowData.winRatePretty != null) {
						        	tr.find('td.total-price').append(rowData.winRatePretty);
						        }
						    });
						},0)
					).done(function(){
						setTimeout(function(){
							$.Pages.initTooltipPlugin();
						},1);
					});
					$('h6.data-tables-header').html(json.iTotalRecords + ' Event' + (json.iTotalRecords > 1 ? 's' : ''));
					return json.data;
				}
	        },
	        initComplete: function(settings, json) {
			      $('#forecastTable_wrapper > .top').prepend($('.range-buttons').removeClass('hidden'));
			}
	    }
	}

    const _linkCalendar = {
        init: () => {
            _dom.on('click', '.go-to-range', function() {
                let range = $(this).data('range');
                $.when(
                    localStorage.setItem('range', range)
                ).done(function(){
                    window.location.href = "/forecast/calendar";
                })
            });
        }
    }

	return {
		init: _init()	
	}
})();