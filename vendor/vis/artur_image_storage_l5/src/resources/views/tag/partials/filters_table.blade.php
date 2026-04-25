 <thead>
    <tr>
        <th width="1%">#</th>
        <th width="25%">{{__cms("Название")}}</th>
        <th width="10%">{{__cms("Создана (от)")}}</th>
        <th width="10%">{{__cms("Создана (до)")}}</th>
        <th width="10%">{{__cms("Тег активен")}}</th>
        <th width="8%">
            <a class="btn btn-sm btn-success" onclick="ImageStorage.getEditFormInTable();">
                <i class="fa fa-plus"></i> {{__cms('Создать')}}
            </a>
        </th>
    </tr>
    <tr class="image-storage-filters-row">
        <td>
        </td>
        <td>
            <div class="relative">
                <input type="text"
                       value="{{Session::get('image_storage_filter.tag.filterByTitle')}}"
                       name="image_storage_filter[filterByTitle]"
                       class="form-control input-small">
            </div>
        </td>
        <td>
            <div>
                <input type="text"
                       id="f-datepicker-from"
                       value="{{Session::get('image_storage_filter.tag.filterByDate.from')}}"
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
                       value="{{Session::get('image_storage_filter.tag.filterByDate.to')}}"
                       name="image_storage_filter[filterByDate][to]"
                       class="form-control input-small datepicker" >
                <span class="input-group-addon form-input-icon form-input-filter-icon">
                    <i class="fa fa-calendar"></i>
                </span>
            </div>
        </td>
        <td>
            <select name="image_storage_filter[filterByActivity][]" multiple="multiple" class="image-storage-select">
                <option value="1"
                        {{in_array(1,Session::get('image_storage_filter.tag.filterByActivity', [])) ? "selected" : ""}}>
                    {{__cms('Активен') }}</option>
                <option value="0"
                        {{in_array(0,Session::get('image_storage_filter.tag.filterByActivity', [])) ? "selected" : ""}}>
                    {{__cms('Не активен') }}</option>
            </select>
        </td>
        <td>
            <button class="btn btn-default btn-sm image-storage-button"
                    type="button"
                    onclick="ImageStorage.doSearchInTable();">
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
