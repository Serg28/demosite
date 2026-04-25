<div class="radio-row" wire:key="payment-input-{{$payment['id']}}">
    <label for="payment-input-{{$payment['id']}}" class="radio flex v--center">
        <input type="radio" name="pay_method_id" data-name="{!! strip_tags(e($payment['name'])) !!}" for="payment-input-{{$payment['id']}}" wire:model="pay_method_id" value="{{$payment['id']}}"
               wire:click="selectPayment({{$payment['id']}})">
        <span class="fw-600 flex v--center" wire:click="selectPayment({{$payment['id']}})">{!! strip_tags($payment['name']) !!}
            @if(@$payment['picture'])
                <div class="card-images flex v--center ml-8">
                       <span class="img"><img src="{{$payment['picture']}}" alt="{{e(strip_tags($payment['name']))}}"></span>
                   </div>
            @endif
            </span>
    </label>
    <div class="fsz-12 color--gray mt-8 info">
        {!!  $payment['short_description']!!}
        @if($pay_method_id === $payment['id'])
            <div class="bank-droppdown droppdown-wrapper mt-16" style="display: block; opacity: 1.045;">
                <div class="wrapper flex fd--column">
                    <div class="row p-16 br--br-4 current">
                        <div class="visible flex v--center">
                            @if(@$payment['picture'])
                            <div class="logo">
                                <img src="{{$payment['picture']}}" alt="">
                            </div>
                            @endif
                            <div class="right">
                                <p class="fsz-14">{!! strip_tags($payment['name']) !!}</p>
                                {{-- <p class="fsz-12 color--gray">Від 10 230₴ / міс., 3 - 4 платежі</p> --}}
                            </div>
                        </div>
                        <div class="hidden">
                            <div class="flex-row flex v--center">
                                <lebel class="input small select">
                                    <select wire:model.change="payparts_count">
                                        @foreach(range($payment['availablePartsCount'], 25) as $number)
                                            <option value="{{$number}}">
                                                {{$number}} {{trans_choice(__t('{0}платежей|[1]платеж|[2,4]платежа|[5,*]платежей'),$number)}}
                                            </option>
                                        @endforeach
                                    </select>
                                </lebel>
                                <div class="text">
                                    @if(isset($payPartsData['monthlyPayment']))
                                    <p class="fsz-14">по <strong>{{$payPartsData['monthlyPayment']}} {{setting('currency')}}</strong></p>
                                    @endif
                                    @if(isset($payPartsData['commissionPercentage']) && $payPartsData['commissionPercentage']>0)
                                    <p class="fsz-12 color--gray">{{__t('Комісія')}} {{$payPartsData['commissionPercentage'] ?? 0}}%</p>
                                    @endif
                                </div>
                            </div>
                            {{--
                            <div class="question flex relative v--center mt-16">
                                <div class="vis flex v--center fsz-14 color--blue">
                                    <img src="assets/images/question.svg" alt="">Як скористатись
                                </div>
                                <div class="hid">
                                    {!!  str_replace(['[isMonoParts]','[isMonoParts2]'], [$payparts_count , $payparts_count-1], setting('opisaniya-oplata-chastyami-monobank-dlya-tovara')) !!}
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

