<div class="image-storage-process-popup" >
    <div class="image-storage-smoke"></div>
    <div class="image-storage-process-block">

        <h2>{{__cms("Процесс загрузки")}}</h2>
        <hr>
        <dl class="dl-horizontal">
          <dt>{{__cms("Успешно")}}</dt>
          <dd><span class="image-storage-upload-success">0</span></dd>
          <dt>{{__cms("Неуспешно")}}</dt>
          <dd><span class="image-storage-upload-fail">0</span></dd>
          <dt>{{__cms("Загружено / всего")}}</dt>
          <dd><span class="image-storage-upload-upload">0</span> / <span class="image-storage-upload-total">0</span></dd>
        </dl>
        <div class="image-storage-progress-bar progress progress-sm progress-striped active">
            <div class="image-storage-progress-fail progress-bar bg-color-redLight" style="width: 0%"></div>
            <div class="image-storage-progress-success progress-bar bg-color-greenLight" style="width: 0%"></div>
        </div>
        <hr>

        <a href="javascript:void(0);" onclick="ImageStorage.doResetUploadPreloader(this);" class="btn btn-info image-storage-upload-finish-btn">{{__cms("Готово")}}</a>
        
    </div>
    
</div>
