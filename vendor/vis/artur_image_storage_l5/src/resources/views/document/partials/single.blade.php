<div class="superbox-img superbox-file"
     data-id="{{ $entity->id }}"
     data-source="{{ asset($entity->getSource()) }}"
     data-createdat="{{ $entity->created_at }}">
    <div class="file-extension" data-ext="{{$entity->getFileExtension()}}"></div>
    <div class="file-title">{{ $entity->title }}</div>
</div>
