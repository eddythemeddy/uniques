(function() {

	var _ingredientTable = $('#forecastTable'),
		_tableObj = null;

	function _init(){
		_dataTables.init();
		$(document).on('change','.filter-active,.filter-state',function(){
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
				[ 1, 'DESC' ]
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
			dom: '<"top"f>rt<"bottom"ipl><"clear">',
		    serverSide: true,
		    // searching: false,
		    processing: false,
		    paging: true,
			pageLength: 10,
	        ajax: {
	            url: location.href,
            	type: "POST",
	            data: function ( d, callback) {
		            	d.fetchForecast = 1;
		            	d.listAll = 1;
		                d.event_type = $('.filter-active').val();
		                d.state = $('.filter-state').val();
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
	        	$('<label class="pull-left m-r-10">Event Status<br/>' +
			        '<select class="filter-state">' +
				        '<option value="all">All</option>' +
				        '<option value="past">Past</option>' +
				        '<option value="upcoming" selected>Upcoming</option>' +
			        '</select>' + 
			      '</label>'+
			      '<label class="pull-left m-r-10">Event Type<br/>' +
			        '<select class="filter-active">' +
				        '<option value="">All</option>' +
				        '<option value="public">Public</option>' +
				        '<option value="private">Private</option>' +
			        '</select>' + 
			      '</label><br/>').appendTo("#forecastTable_filter");
			}
	    }
	}

	return {
		init: _init()	
	}
})();