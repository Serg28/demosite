@extends('layouts.default')

@section('seo_tags')
    <title>{{__t('Кешбек')}}</title>
@stop

@section('main')

    <div class="account-page">
        <div class="container">
            <div class="account-page__wrap flex v--start h--between">
                @include('profile.partials.sidebar',['sell' => 'discount'])

                <div class="account-page__content">
                    <h2 class="fsz-28 fw-600 mb-24 content-heading">Кешбек</h2>
                    <div class="account-cashback">
                        <div class="top-row p-24 br--br-4 bg--white">
                            <p>Доступно кешбеку</p>
                            <div class="cb flex v--center">
                                <img src="/assets/images/cb.svg" alt="">
                                <span class="fsz-24 fw-600">815 ₴ </span>
                            </div>
                            <div class="row flex v--center mt-16">
                                <span class="color--gray fsz-14">Кешбек - повернення 10% вартості покупки</span>
                                <span class="color--gray fsz-14">Сплачуй до 50% вартості замовлення</span>
                            </div>
                        </div>
                        <div class="history-cash mt-24">
                            <p class="fsz-20 fw-600">Історія коштів</p>
                            <div class="history-cash__wrap mt-24 p-24 br--br-4 bg--white">
                                <table>
                                    <tr>
                                        <th>Замовлення №</th>
                                        <th>Дата</th>
                                        <th>Списано</th>
                                        <th>Нараховано</th>
                                    </tr>
                                    <tr>
                                        <td>36191260</td>
                                        <td>23.08.2023</td>
                                        <td class="orange">- 500 ₴</td>
                                        <td>+ 482 ₴</td>
                                    </tr>
                                    <tr>
                                        <td>36191260</td>
                                        <td>23.08.2023</td>
                                        <td>0</td>
                                        <td>+ 482 ₴</td>
                                    </tr>
                                    <tr>
                                        <td>36191260</td>
                                        <td>23.08.2023</td>
                                        <td class="orange">- 500 ₴</td>
                                        <td>+ 482 ₴</td>
                                    </tr>
                                    <tr>
                                        <td>36191260</td>
                                        <td>23.08.2023</td>
                                        <td class="orange">- 500 ₴</td>
                                        <td>+ 482 ₴</td>
                                    </tr>
                                    <tr>
                                        <td>36191260</td>
                                        <td>23.08.2023</td>
                                        <td>0</td>
                                        <td>+ 482 ₴</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="how-it-work mt-24 br--br-4 bg--white p-24">
                            <div class="visible">
                                <p class="fsz-18 fw-600">Як це працює?</p>
                                <p class="mt-4 fsz-14 color--gray">Пояснюємо, що таке кешбек та як він працює</p>
                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="9" viewBox="0 0 14 9" fill="none">
                                        <path d="M1 1L7 7L13 1" stroke="#2264DC" stroke-width="2"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="hidden mt-24 pt-24">
                                <div class="columns flex v--start">
                                    <div class="col">
                                        <div class="img">
                                            <img src="/assets/images/cb-i1.svg" alt="">
                                        </div>
                                        <div class="desc">
                                            <p class="fw-600 mt-16">Обирай товари</p>
                                            <p class="mt-4 color--gray sub">Заходь на сайт SmartMag, обирай будь-які товари</p>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="img">
                                            <img src="/assets/images/cb-i2.svg" alt="">
                                        </div>
                                        <div class="desc">
                                            <p class="fw-600 mt-16">Зроби покупку</p>
                                            <p class="mt-4 color--gray sub">Купуй товари та послуги, сплачуй як тобі зручно</p>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="img">
                                            <img src="/assets/images/cb-i3.svg" alt="">
                                        </div>
                                        <div class="desc">
                                            <p class="fw-600 mt-16">Отримай кешбек</p>
                                            <p class="mt-4 color--gray sub">Отримуй частину витрачених коштів назад</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="desc mt-40">
                                    <p class="fsz-18 fw-600">Кмітливі купують з кешбеком — не зволікай і ти</p>
                                    <ol class="flex fd--column">
                                        <li>10% від вартості замовлення від кожної Вашої покупки буде відкладено на Ваш рахунок</li>
                                        <li>На що можеш використати накопичені бонуси? На знижки до -50% на наступні покупки!</li>
                                        <li>Коштами Ви можете сплатити до половини вартості товару - новинок й тих, що вже на розпродажі.</li>
                                    </ol>
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
    <link rel="stylesheet" href="{{mix('/assets/css/pages/account-cashback.min.css')}}">
@endpush
@push('footer-styles')
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush