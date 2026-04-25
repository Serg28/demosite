<div class="superbox-file">
    <div class="file-extension" data-ext="{{$entity->getFileExtension($ident)}}"></div>
    <div class="file-title">
        <div>
            <span class="file-label">{{ __cms("Название файла:")}}</span>
            {{$entity->getFileName($ident)}}
        </div>
        <div>
            <span class="file-label">{{ __cms("Размер файла:")}}</span>
            {{$entity->getFileSize($ident)}}
        </div>
        <div>
            <span class="file-label">{{ __cms("Тип файла:")}}</span>
            [{{$entity->getFileMimeType($ident)}}]
        </div>
        <div>
            <span class="file-label">{{ __cms("Ссылка на файл:")}}</span>
            {{asset($entity->getSource($ident))}}
        </div>
    </div>
</div>
