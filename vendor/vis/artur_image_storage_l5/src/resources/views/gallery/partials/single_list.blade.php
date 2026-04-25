<tr class="tr_{{$entity->id}} " id_page="{{$entity->id}}">
    <td>
        {{$entity->id}}
    </td>
    <td>
        {{$entity->title}}
    </td>
    <td colspan="2">{{$entity->created_at}}</td>
    <td>
        @forelse($entity->tags as $key=>$tag)
            <span>{{$tag->title}} </span>
        @empty
            <span class="glyphicon glyphicon-minus"></span>
        @endforelse
    </td>
    <td>
<span>
    @if ($entity->is_active)
        <span class="glyphicon glyphicon-ok"></span>
    @else
        <span class="glyphicon glyphicon-minus"></span>
    @endif
</span>
    </td>
    <td>
        <div class="btn-group hidden-phone pull-right">
            <a class="btn dropdown-toggle btn-xs btn-default"  data-toggle="dropdown"><i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i></a>
            <ul class="dropdown-menu pull-right" id_rec ="{{$entity->id}}">
                <li>
                    <a class="edit_record" onclick="ImageStorage.getEditFormInTable({{$entity->id}})"><i class="fa fa-pencil"></i> {{__cms('Редактировать')}}</a>
                </li>
                <li>
                    <a onclick="ImageStorage.doDeleteInTable({{$entity->id}});"><i class="fa red fa-times"></i> {{__cms("Удалить")}}</a>
                </li>
            </ul>
        </div>
    </td>
</tr>
