@extends('layouts.default')

@section('seo_tags')
    <title>{{__t('Підписки')}}</title>
@stop

@section('main')

    <div class="account-page">
        <div class="container">
            <div class="account-page__wrap flex v--start h--between">
                @include('profile.partials.sidebar',['sell' => 'subscriptions'])

                <div class="account-page__content">
                    <h2 class="fsz-28 fw-600 mb-24 content-heading">Підписки</h2>
                    <div class="account-subs p-24 br--br-4 bg--white">
                        <p class="fsz-18 fw-600">Види розсилок</p>
                        <div class="top-row flex fd--column">
                            <div class="checkbox-row flex v--start">
                                <label for="input-1" class="checkbox">
                                    <input type="checkbox" id="input-1">
                                </label>
                                <div class="right">
                                    <p>Новини</p>
                                    <span class="fsz-12 color--gray">Новини компанії та пропозиції співпраці</span>
                                </div>
                            </div>
                            <div class="checkbox-row flex v--start">
                                <label for="input-2" class="checkbox">
                                    <input type="checkbox" id="input-2">
                                </label>
                                <div class="right">
                                    <p>Маркетингові пропозиції</p>
                                    <span class="fsz-12 color--gray">Періодично ми проводимо маркетингові активності та акції зі знижками, розіграшами, промокодами та іншими інструментами</span>
                                </div>
                            </div>
                            <div class="checkbox-row flex v--start">
                                <label for="input-3" class="checkbox">
                                    <input type="checkbox" id="input-3">
                                </label>
                                <div class="right">
                                    <p>Рекомендації засновані на клієнтському досвіді</p>
                                    <span class="fsz-12 color--gray">Персональні, своєчасні та вигідні пропозиції саме для Вас</span>
                                </div>
                            </div>
                            <div class="checkbox-row flex v--start">
                                <label for="input-4" class="checkbox">
                                    <input type="checkbox" id="input-4">
                                </label>
                                <div class="right">
                                    <p>Опитування</p>
                                    <span class="fsz-12 color--gray">Отримуйте запрошення для участі в опитуваннях, консультаціях і тестуванні інструментів</span>
                                </div>
                            </div>
                        </div>
                        <p class="mt-24 fsz-18 fw-600">Канали зв'язку</p>
                        <div class="bottom-row flex fd--column mt-24">
                            <div class="checkbox-row flex v--start">
                                <label for="input-5" class="checkbox">
                                    <input type="checkbox" id="input-5">
                                </label>
                                <div class="right">
                                    <p>Email-листи</p>
                                </div>
                            </div>
                            <div class="checkbox-row flex v--start">
                                <label for="input-6" class="checkbox">
                                    <input type="checkbox" id="input-6">
                                </label>
                                <div class="right">
                                    <p>Повідомлення у Viber</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/account-history.min.css')}}">
@endpush
@push('footer-styles')
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush