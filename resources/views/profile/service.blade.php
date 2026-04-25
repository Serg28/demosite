@extends('layouts.default')

@section('seo_tags')
    <title>{{__t('SmartMag Cервіс')}}</title>
@stop

@section('main')

    <div class="account-page">
        <div class="container">
            <div class="account-page__wrap flex v--start h--between">
                @include('profile.partials.sidebar',['sell' => 'service'])

                <div class="account-page__content">
                    <h2 class="fsz-28 fw-600 mb-24 content-heading">{{__t('SmartMag Cервіс')}}</h2>
                    <div class="account-service p-24 br--br-4 bg--white">
                        <div class="top-row pb-24">
                            <p class="fw-600 fsz-18">{{__t('Заявка на проведення гарантійного обслуговування')}}</p>
                            <p class="mt-16">{{__t('Заповнюється для кожної одиниці товару, що передається Клієнтом в СЦК транспортом Компанії або з використанням поштових служб.')}}</p>
                        </div>
                        <livewire:profile.form.service subject="{{__t('Заявка на проведення гарантійного  обслуговування')}}" />
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

@push('header')
    <link rel="stylesheet" href="{{mix('/assets/css/pages/account-service.min.css')}}">
@endpush
@push('footer-styles')
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
@endpush
@push('footer-scripts')
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/swiper.js"></script>
@endpush