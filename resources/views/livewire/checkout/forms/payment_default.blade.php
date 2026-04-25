<div class="radio-row" wire:key="payment-input-{{$payment['id']}}" >
        <label for="payment-input-{{$payment['id']}}" class="radio flex v--center">
            <input type="radio" name="pay_method_id" data-name="{!! strip_tags(e($payment['name'])) !!}" for="payment-input-{{$payment['id']}}" wire:model="pay_method_id" value="{{$payment['id']}}" wire:click="selectPayment({{$payment['id']}})">
            <span class="fw-600 flex v--center" wire:click="selectPayment({{$payment['id']}})">{!! strip_tags($payment['name']) !!}
                @if(@$payment['picture'])
                    <div class="card-images flex v--center ml-8">
                       <span class="img"><img src="{{$payment['picture']}}" alt="{{e(strip_tags($payment['name']))}}"></span>
                   </div>
                @endif
            </span>
        </label>
        <div class="fsz-12 color--gray mt-8 info">{!!  $payment['short_description']!!}</div>
</div>

