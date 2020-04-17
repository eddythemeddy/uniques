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
                    $('.inner-content #content').html(d)
                    $('.inner-content .btn')
                        .removeClass('hidden')
                        .text('Open ' + doc)
                        .attr('href', '/' + doc)
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