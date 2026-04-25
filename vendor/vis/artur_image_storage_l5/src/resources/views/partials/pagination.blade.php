<div class="dt-toolbar-footer">
    <div class="col-sm-4 col-xs-12 hidden-xs">
        <div id="dt_basic_info" class="dataTables_info" role="status" aria-live="polite">
            {{__cms('Показано')}}
            <span class="txt-color-darken listing_from">{{$data->firstItem()}}</span>
            -
            <span class="txt-color-darken listing_to">{{$data->lastItem()}}</span>
            {{__cms("из")}}
            <span class="text-primary listing_total">{{$data->total()}}</span>
            {{__cms("записей")}}
        </div>
    </div>
    <div class="col-xs-12 col-sm-4">
        <div id="dt_basic_paginate" class="dataTables_paginate paging_simple_numbers">
            {{$data->links()}}
        </div>
    </div>
</div>
