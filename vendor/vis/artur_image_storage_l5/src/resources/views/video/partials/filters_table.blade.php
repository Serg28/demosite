<form id="image-storage-search-form">
<table class="table  table-hover table-bordered " id="sort_t">
    <thead>
    <tr>
        <th width="25%">{{__cms("Название")}}</th>
        <th width="10%">{{__cms("Создана (от)")}}</th>
        <th width="10%">{{__cms("Создана (до)")}}</th>
        <th width="10%">{{__cms("Связанные видеогалереи")}}</th>
        <th width="10%">{{__cms("Связанные теги")}}</th>
        <th width="8%">
            <a class="btn btn-sm btn-success" onclick="ImageStorage.getEditForm($('#image-storage-search-form'));">
                <i class="fa fa-plus"></i> {{__cms('Создать')}}
            </a>
        </th>
    </tr>
    <tr class="image-storage-filters-row">
        <td>
            <div class="relative">
                <input type="text"
                       value="{{Session::get('image_storage_filter.video.filterByTitle')}}"
                       name="image_storage_filter[filterByTitle]"
                       class="form-control input-small">
            </div>
        </td>
        <td>
            <div>
                <input type="text"
                       id="f-datepicker-from"
                       value="{{Session::get('image_storage_filter.video.filterByDate.from')}}"
                       name="image_storage_filter[filterByDate][from]"
                       class="form-control input-small datepicker" >
                <span class="input-group-addon form-input-icon form-input-filter-icon">
                    <i class="fa fa-calendar"></i>
                </span>
            </div>
        </td>
        <td>
            <div>
                <input type="text"
                       id="f-datepicker-to"
                       value="{{Session::get('image_storage_filter.video.filterByDate.to')}}"
                       name="image_storage_filter[filterByDate][to]"
                       class="form-control input-small datepicker" >
                <span class="input-group-addon form-input-icon form-input-filter-icon">
                    <i class="fa fa-calendar"></i>
                </span>
            </div>
        </td>
        <td>
            <select name="image_storage_filter[filterByVideoGalleries][]" multiple="multiple" class="image-storage-select">
                @foreach($relatedEntities['videoGalleries'] as $videoGallery)
                    <option value="{{$videoGallery->id}}"
                            {{in_array($videoGallery->id,Session::get('image_storage_filter.video.filterByVideoGalleries', [])) ? "selected" : ""}}>
                        {{$videoGallery->title}}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="image_storage_filter[filterByTags][]" multiple="multiple" class="image-storage-select">
                @foreach($relatedEntities['tags'] as $tag)
                    <option value="{{$tag->id}}"
                            {{in_array($tag->id,Session::get('image_storage_filter.video.filterByTags', [])) ? "selected" : ""}}>
                        {{$tag->title}}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <button class="btn btn-default btn-sm image-storage-button"
                    type="button"
                    onclick="ImageStorage.doSearch();">
                {{__cms('Поиск') }}
            </button>
            <button class="btn btn-default btn-sm image-storage-button"
                    type="button"
                    onclick="ImageStorage.doResetFilters();">
                {{__cms('Сбросить') }}
            </button>
        </td>
    </tr>
    </thead>
</table>
</form>
