<div class="image-storage-operations well row">
    <form name="image-storage-operations-form">
    <div class="col-md-12 image-storage-operations-row">
        <div class="col-md-2">{{__cms('Добавить к тегам')}}</div>
        <div class="col-md-8">
            <div class="input-group">
                <select name="relations[image-storage-tags][]" multiple="multiple" class="image-storage-operations-input image-storage-select">
                    @foreach($relatedEntities['tags'] as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <a onclick="ImageStorage.doRelateToTags();" href="javascript:void(0);" class="btn btn-default btn-sm image-storage-operations-button">{{__cms('Добавить')}}</a>
        </div>
    </div>
    <div class="col-md-12 image-storage-operations-row">
        <div class="col-md-2">{{__cms('Удалить документы')}}</div>
        <div class="col-md-8">
        </div>
        <div class="col-md-2">
            <a onclick="ImageStorage.deleteMultipleGridView();" href="javascript:void(0);" class="btn btn-default btn-sm image-storage-operations-button">{{__cms('Удалить')}}</a>
        </div>
    </div>
    <div class="col-md-12 image-storage-operations-row">
        <div class="col-md-2">{{__cms('Отображать документы')}}</div>
        <div class="col-md-8">
        </div>
        <div class="col-md-2">
            <a onclick="ImageStorage.changeMultipleActivity(1);" href="javascript:void(0);" class="btn btn-default btn-sm image-storage-operations-button">{{__cms('Отображать')}}</a>
        </div>
    </div>
    <div class="col-md-12 image-storage-operations-row">
        <div class="col-md-2">{{__cms('Спрятать документы')}}</div>
        <div class="col-md-8">
        </div>
        <div class="col-md-2">
            <a onclick="ImageStorage.changeMultipleActivity(0);" href="javascript:void(0);" class="btn btn-default btn-sm image-storage-operations-button">{{__cms('Спрятать')}}</a>
        </div>
    </div>
    </form>
</div>
