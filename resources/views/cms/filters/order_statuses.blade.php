<p>
    <select name="filter[order_status_id]" style="width: 170px;">
        <option value="">Статус</option>
        @foreach($statuses as $status)
            <option value="{{$status->id}}"
            {{isset($filtersCurrent['order_status_id']) && $filtersCurrent['order_status_id'] == $status->id ? 'selected' : ''}}
            >{{$status->t('title')}}</option>
        @endforeach
    </select>
</p>
<p>
    <select name="filter[complect_status_id]" style="width: 170px;">
        <option value="">Статус комплектации</option>
        @foreach($complectations as $complectation)
            <option value="{{$complectation->id}}"
            {{isset($filtersCurrent['complect_status_id']) && $filtersCurrent['complect_status_id'] == $complectation->id ? 'selected' : ''}}
            >{{$complectation->t('title')}}</option>
        @endforeach
    </select>
</p>
<p>
    <select name="filter[is_online_payed]" style="width: 170px;">
        <option value="">Статус оплаты</option>
        @foreach($paymentStatuses as $paymentStatus)
            <option value="{{$paymentStatus->id}}"
            {{isset($filtersCurrent['is_online_payed']) && $filtersCurrent['is_online_payed'] == $paymentStatus->id ? 'selected' : ''}}
            >{{$paymentStatus->t('title')}}</option>
        @endforeach
    </select>
</p>