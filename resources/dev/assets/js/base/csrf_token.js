(function ($) {
    if ($('meta[name="csrf-token"]').attr('content')) {
        /* $.ajaxSetup({ //jquery
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
        });*/
        $.ajaxSettings = { //zepto
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }
    }
});