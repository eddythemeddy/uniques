(function() {

	let _modal = $('#modalSlideUp'),
		_transactionTable = $('#transactionsTable'),
		_frm = $('#form-modal-transaction'),
        _btn = _frm.find('button[type="submit"]');

	function _init() {
		$(document).on('change mousewheel','#advance', function(){
			if($(this).val() < 0) {
				$(this).val(0);
			}
		});
		$(function () {
            $('#date-of-payment').datepicker();
        });

        $.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                var check = false;
                return this.optional(element) || regexp.test(value);
            },
            "Please check your input."
        );
	}

	const _loadTransactions = {
		load: () => {
			$.ajax({
	            dataType: "json",
	            type: "POST",
	            data: {
	            	loadTransactions: 1
	            },
	            beforeSend: () => {
	            },
	            success: (d) => {
	            	_loadTransactions.build(d)
	            	_transactionTable.removeClass('loading');
	            },
	            error: () => {
	            	_transactionTable.removeClass('loading');
	            }
	        });
		},
		build: (data) => {
			$.when(
            	_transactionTable.addClass('loading'),
            	_transactionTable.find('tbody').html('')
	        ).done(function(){
	        	$.each(data.transactions,function(i,e){
	        		_transactionTable.find('tbody').append(
	        			'<tr>' +
	        				'<td>' + e.date + '</td>' +
	        				'<td>' + e.reference + '</td>' +
                            '<td>' + e.method + '</td>' +
	        				'<td>' + e.name + '</td>' +
	        			'</tr>'
	        		);
	        	})
	        }).then(function(){
	        	_transactionTable.removeClass('loading');
	        })
		}
	}

    $(document).on('show.bs.modal','#modalSlideUp', function () {
        $.when(
        	_loadTransactions.load()
        ).done(function() {
	        _validate.init();
	        _submit.init();
        })
    });

    var _validate = {
        options: {
            ignore: [],
            errorElement: 'span',
            errorPlacement: (error, element) => {
                if(error.length > 0) {
                    error.insertBefore(element);
                }
                return false;
            },
            invalidHandler: (form, validator) => {
                if(!validator.numberOfInvalids()) {
                    return;
                }
            },
            rules: {
                'date-of-payment': {
                	required: true,
                    regex : /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/
                },
                'transaction-reference': {
                    required: true
                },
                'method': {
                    required: true
                }
            }
        },
        init: function() {
            _frm.validate(this.options);
        }
    };

    const _submit = {
        options: {
            url: location.href,
            type: 'post',
            dataType: 'json',
            beforeSubmit: (arr, form, options) => {
                _submit.action.buttonOff(),
                $('.pgn-wrapper').remove();
                return _frm.valid(); //call the validate plugin
            },
            success: (d) => {
            	_frm[0].reset();
            	if(d.r != 'danger') {
            		_totalPaid.html('$' + d.totalPaid),
                	_loadTransactions.build(d);
            	}
                _submit.action.notify(d.r, d.message),
                _submit.action.buttonOn();
            },
            error: (d) => {
                _submit.action.buttonOn();
            },
            always: () => {
            	_frm[0].reset();
            }
        },
        init: function() {
            _frm.ajaxForm(this.options);
        },
        action: {
            buttonOn: ()=> {
                _btn.removeClass('disabled').removeAttr('disabled');
            },
            buttonOff: ()=> {
                _btn.addClass('disabled').attr('disabled','disabled');  
            },
            notify: (type, message)=> {
                $('body').pgNotification({
                    style: 'flip',
                    message: message,
                    position: 'top-right',
                    timeout: 0,
                    type: type
                }).show(),
                setTimeout(()=> {
                    $('.pgn-wrapper').remove();
                },3000);
            }
        }
    };

	return {
		init: _init()
	}
})();