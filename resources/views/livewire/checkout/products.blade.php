<div x-data="{ open: false }">
    <div class="top-row flex p-16 v--center h--between">
        <p class="fw-600 fsz-18">{{__t('Ваше замовлення')}}</p>
        <span class="color--blue get-edit-order_" @click.prevent="open = ! open">{{__t('Редагувати')}}</span>
    </div>
    <div class="middle-row p-16 flex fd--column" {{-- data-simplebar data-simplebar-auto-hide="false" wire:ignore --}}>
        @if($productsInCart->isNotEmpty())

            @foreach ($productsInCart as $product)
                @php
                    $model = $product->model;
                @endphp
                <div class="prod-row flex v--center h--between" wire:key="cart-row-{{ $product->rowId }}" x-bind:class="open ? 'active' : ''">
                    <div class="trash flex v--center h--center" wire:click="remove('{{ $product->rowId }}')" wire:loading.attr="disabled">
                        <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 10V16M14 10V16M18 6V18C18 19.1046 17.1046 20 16 20H8C6.89543 20 6 19.1046 6 18V6M4 6H20M15 6V5C15 3.89543 14.1046 3 13 3H11C9.89543 3 9 3.89543 9 5V6" stroke="#AFB1C4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <a href="{{ $model->getUrl() }}" class="image flex v--center h--center">
                        @if(!empty($model->picture))
                            {!! $model->getImg(120, 120) !!}
                        @else
                            <img src="{!! glide($model->firstOtherPicture, ['w'=>120, 'h'=>120]) !!}" alt="{{ e($model->t('title')) }}">
                        @endif
                    </a>
                    <div class="right">
                        <a href="" class="name color--black fsz-14 lh-140">{{ $model->t('title') }}</a>
                        <div class="price flex v--center mt-4">
                            @if($model->getPriceOld())
                                <s class="fsz-14 color--gray">@money($model->getPriceOld()) {{ setting('currency') }}</s>
                            @endif
                            <p class="fsz-14 fw-600 _ml-12">@money($product->price) {{ setting('currency') }}</p>
                        </div>
                        <div class="calc-wrap flex v--center mt-12">
                            <div class="calc">
                                <div class="input-group flex v--center">
                                    <button class="minus-item_ @if($product->qty === 1) disabled @endif" wire:click="decrementQuantity('{{ $product->rowId }}')" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M8 12.5H17" stroke="#0A0527"/>
                                            <circle cx="12.5" cy="12.5" r="9" stroke="#0A0527"/>
                                        </svg>
                                    </button>
                                    <input type="text" wire:model.lazy="quantities.{{$product->rowId}}" onkeypress="return isNumberKey(event)" wire:loading.attr="disabled" class="item-quantity" maxlength="3" autocomplete="off" readonly>
                                    <button class="plus-item_ @if($model->getQuantity()===$product->qty) disabled @endif" wire:click="incrementQuantity('{{ $product->rowId }}')" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M12.5 8V12.5M12.5 12.5H8M12.5 12.5V17M12.5 12.5H17" stroke="#0A0527"/>
                                            <circle cx="12.5" cy="12.5" r="9" stroke="#0A0527"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>