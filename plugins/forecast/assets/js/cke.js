(function(){

    _init = () => {
        $('[data-document]').on('click',function(){
            let doc = $(this).data('document');
            $.ajax({
                type: "GET",
                data: { 
                    document: doc
                },
                success: (d) => {
                    $('.inner-content').html(d)
                },
                error: () => {
                }
            });
        })
    }

    return {
        init: _init()
    };

})();