
@forelse ($listingRecords as $record)
    @include('cms.fields.ordertable_single_row')
@empty
    <tr>
        <td colspan="100%">{{__cms('Пока пусто')}}</td>
    </tr>
@endforelse
