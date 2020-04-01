var LOGIN = (function(){

    var _frm = $('#form-email'),
        _btn = _frm.find('button[type="submit"]');

    function _init() {
        _validate.init();
        _submit.init();
    }

    var _validate = {
        options: {
            ignore: [],
            errorElement: 'span',
            errorPlacement: function(error, element) {
                if(error.length > 0) {
                    error.insertBefore(element);
                }
                return false;
            },
            invalidHandler: function(form, validator) {
                if(!validator.numberOfInvalids()){
                    return;
                }
            },
            rules: {
                email: {
                    required: "required",
                    email: true
                },
                password: "required",
                subject: "required"
            }
        },
        init: function() {
            _frm.validate(this.options);
        }
    };

    var _submit = {
        options: {
            url: location.href,
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(arr, form, options) {
                _submit.action.buttonOff(),
                $('.alert.form-notification').remove();
                return _frm.valid(); //call the validate plugin
            },
            success: function(d) {
                if(d.r == "fail") {
                    _submit.action.notify(d.type, d.message);
                    _submit.action.buttonOn();
                } else {
                    window.location = d.redirect;
                }
                _submit.action.buttonOn();
            },
            error: function(d) {
                _submit.action.buttonOn();
            }
        },
        init: function() {
            _frm.ajaxForm(this.options);
        },
        action: {
            buttonOn: function() {
                _btn.removeClass('disabled').removeAttr('disabled');
            },
            buttonOff: function() {
                _btn.addClass('disabled').attr('disabled','disabled').text('Sending Mail, please dont refresh');  
            },
            notify: function(type, message) {
                _frm.prepend(
                    '<div class="alert alert-' + type + ' form-notification" role="alert">' +
                    '<button class="close" data-dismiss="alert"></button>' + message 
                ),
                setTimeout(function(){
                    $('.alert.form-notification').remove();
                },3000);
            }
        }
    };

    return {
        init: _init()
    };
})();