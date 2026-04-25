<div class="cart-block" style="height: auto; max-height: 390px">
    @if($productsInCart->isNotEmpty())

        @foreach ($productsInCart as $product)
            @php
                $model = $product->model;
            @endphp

                <div class="cart-row">
                    <a href="{{ $model->getUrl() }}" class="left">
                        {!! $model->getImg(120, 120) !!}
                    </a>
                    <div class="right">
                        <a href="{{ $model->getUrl() }}" class="name">{{ $model->t('title') }}</a>
                        <div class="flex-row">
                            <span class="article">{{ __t('Артикул') }}: {{$model->getArticle() ?: '-'}}</span>
                            {{--<span class="part">{{__t('Парт №')}}: {{$model->part_number ?: '-'}}</span> --}}
                        </div>
                        <div class="calc">
                            <div class="input-group">
                                <button class="minus-item_ @if($product->qty === 1) disabled @endif" type="button"
                                        wire:click="decrementQuantity('{{ $product->rowId }}')" wire:loading.attr="disabled">
                                    <svg class="delete" width="15" height="19" viewBox="0 0 15 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M5.56759 3.86798C5.71696 2.98216 6.51052 2.28333 7.50003 2.28333C8.48953 2.28333 9.28309 2.98216 9.43247 3.86798H5.56759ZM4.05581 3.86798C4.21265 2.12388 5.71131 0.783325 7.50003 0.783325C9.28874 0.783325 10.7874 2.12388 10.9442 3.86798H13.6815C14.0957 3.86798 14.4315 4.20377 14.4315 4.61798C14.4315 5.03219 14.0957 5.36798 13.6815 5.36798H1.31854C0.904329 5.36798 0.568542 5.03219 0.568542 4.61798C0.568542 4.20377 0.904329 3.86798 1.31854 3.86798H4.05581ZM6.48071 7.3139C6.48071 6.89969 6.14493 6.5639 5.73071 6.5639C5.3165 6.5639 4.98071 6.89969 4.98071 7.3139L4.98071 14.801C4.98071 15.2152 5.3165 15.551 5.73071 15.551C6.14493 15.551 6.48071 15.2152 6.48071 14.801L6.48071 7.3139ZM10.0193 7.3139C10.0193 6.89969 9.6835 6.5639 9.26929 6.5639C8.85507 6.5639 8.51929 6.89969 8.51929 7.3139L8.51929 14.801C8.51929 15.2152 8.85507 15.551 9.26929 15.551C9.6835 15.551 10.0193 15.2152 10.0193 14.801V7.3139ZM2.94745 7.3139C2.94745 6.89969 2.61166 6.5639 2.19745 6.5639C1.78324 6.5639 1.44745 6.89969 1.44745 7.3139V14.6913C1.44745 16.7624 3.12638 18.4413 5.19744 18.4413H9.87633C11.9474 18.4413 13.6263 16.7624 13.6263 14.6913V7.3139C13.6263 6.89969 13.2905 6.5639 12.8763 6.5639C12.4621 6.5639 12.1263 6.89969 12.1263 7.3139V14.6913C12.1263 15.934 11.119 16.9413 9.87633 16.9413H5.19744C3.95481 16.9413 2.94745 15.934 2.94745 14.6913V7.3139Z"
                                              fill="#171A20"/>
                                    </svg>
                                    <svg class="minus" width="10" height="2" viewBox="0 0 10 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M1.74845e-07 -3.97627e-08L10 8.34465e-07L10 2L0 2L1.74845e-07 -3.97627e-08Z"
                                              fill="#171A20"/>
                                    </svg>
                                </button>
                                <input type="text" wire:model.lazy="quantities.{{$product->rowId}}"
                                       class="item-quantity"
                                       onkeypress="return isNumberKey(event)" maxlength="3" wire:loading.attr="disabled">
                                <button class="plus-item_" type="button"
                                        wire:click="incrementQuantity('{{ $product->rowId }}')"
                                        @if($model->getQuantity()===$product->qty) disabled @endif wire:loading.attr="disabled">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M4 10L4 -8.74206e-08L6 0L6 10L4 10Z" fill="#171A20"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M1.74845e-07 4L10 4L10 6L0 6L1.74845e-07 4Z" fill="#171A20"/>
                                    </svg>
                                </button>
                                <p>{{ setting('currency') }}<span>@money($product->price)</span></p>
                            </div>
                        </div>
                    </div>
            </div>
        @endforeach
    @endif
</div>