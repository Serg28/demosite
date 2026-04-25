@if($deliveries->isNotEmpty() && !empty($city_id))
        {{-- Список методов доставки --}}
        @foreach($deliveries as $delivery)
            <div class="radio-row p-16 br--br-4 @if($delivery_id === $delivery['id']) current @endif" wire:key="delivery-input-{{$delivery['id']}}">
                <div class="flex-row flex v--center h--between">
                    <label for="delivery-input-{{$delivery['id']}}" class="radio flex v--center">
                        <input type="radio" for="delivery-input-{{$delivery['id']}}"
                               id="delivery-input-{{$delivery['id']}}" @if($delivery_id === $delivery['id']) checked @endif
                               wire:click="selectDelivery({{$delivery['id']}}, '{{$delivery['type']}}')"
                               name="delivery_id" value="droppdown-wrap-1" data-name="{{e($delivery['title'])}}">
                        <span class="fw-600">{{$delivery['title']}}</span>
                    </label>
                    @if(!empty($delivery['price']))
                    <p class="fw-600 fsz-14 color--green">{{$delivery['price']}}</p>
                    @endif
                </div>

                @if($delivery_id === $delivery['id'])
                    {{-- Форма выбранного метода доставки для заполнения --}}
                    {!! $delivery_form !!}
                    {{-- / Форма выбранного метода доставки для заполнения --}}
                @endif
                @if($delivery['description'])
                    <div class="fsz-12 color--gray mt-8 info">{!! $delivery['description'] !!}</div>
                @endif
            </div>
        @endforeach
        @error('delivery_id') <div class="error">{{__t('Выберіть доставку')}}</div> @enderror
        {{-- / Список методов доставки --}}
    {{-- Форма выбранного метода доставки для заполнения --}}
    {{-- {!! $delivery_form !!} --}}
    {{-- / Форма выбранного метода доставки для заполнения --}}
@endif
