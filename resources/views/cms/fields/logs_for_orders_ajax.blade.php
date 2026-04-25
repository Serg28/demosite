<table style="width: 100%" class="logs_table revisions table  table-hover table-bordered">
    <thead>
    <tr>
        <td width="15%">{{__cms("Менеджер")}}</td>
        <td>{{__cms("Поле")}}</td>
        <td width="20%">{{__cms("Старое значение")}}</td>
        <td width="20%">{{__cms("Новое значение")}}</td>
        <td width="20%">{{__cms("Дата и время")}}</td>
        <td width="10%"></td>
    </tr>
    </thead>
    <tbody>
    @foreach($logs as $log)

        {{--@if($log->newValue()!='' and $log->oldValue()!=0 && $log->fieldName()!=='np_info') --}}
        @if($log->new_value!=='' && $log->old_value!==0 && $log->key!=='np_info')

            <?php
                $fieldname = $log->fieldName();
                switch ($fieldname) {
                    case 'LiqPay':
                    case 'LiqPayCOD':
                        $oldValue = json_decode($log->oldValue());
                        //$oldValue = 'ID: '.$oldValue->order_id.': '.__cms('liqpay_status_'.$oldValue->status);
                        $oldValue = $oldValue ? 'ID: '.$oldValue->order_id.': '.__cms('liqpay_status_'.$oldValue->status) : '';
                        $newValue = json_decode($log->newValue());
                        //$newValue = 'ID: '.$newValue->order_id.': '.__cms('liqpay_status_'.$newValue->status);
                        $newValue = $newValue ? 'ID: '.$newValue->order_id.': '.__cms('liqpay_status_'.$newValue->status) : '';
                        break;


                        case 'PrivatPayParts':
                           $oldValue = json_decode($log->oldValue());
                            //$oldValue = json_decode($log->old_value);
                           $oldValue = (!empty($oldValue->state)) ?
                               'ID: '.$oldValue->orderId.': Создание оплаты '.$oldValue->state :
                               'ID: '.$oldValue->orderId.': Результат оплаты '.@$oldValue->paymentState; /**/
                        break;
                    default:
                        $oldValue = $log->oldValue();
                        $newValue = $log->newValue();
                        //$oldValue = $log->old_value;
                        //$newValue = $log->new_value;
                        break;
                }
            ?>


            <tr>
                <td>
                    {{--$log->user_id ? $log->userResponsible()->getFullName(): __cms("Система")--}}
                    {{$log->user_id ? $log->first_name .' '.$log->last_name : __cms("Система")}}
                </td>
                <td>
                    {!! $fieldname !!}
                </td>
                <td>
                    {!! $oldValue !!}
                </td>
                <td>
                    {!! $newValue !!}
                </td>
                <td>
                    {{$log->created_at->format('d.m.Y H:i:s')}}
                </td>
                <td>
                    <a onclick="TableBuilder.getReturnHistory({{$log->id}});">{{__cms("Вернуть изменения")}}</a>
                </td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
