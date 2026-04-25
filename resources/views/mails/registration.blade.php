@extends('layouts.mail')

@section('main')

    <p>{{__t('Вітаємо Вас')}}, {{ $user->first_name }}!</p>
    <p>{!! str_replace(['[sitename]','[url]'], [config('app.name'), getUrl('/')], __t('Для Вас був створений обліковий запис на сайті <a href="[url]" style="color:#2264dc">[sitename]</a>')) !!}.</p>
    <p>{{ __t('Ваш пароль для входу') }}: <strong>{{$password }}</strong></p>
    <p>{{ __t('Для завершення реєстрації перейдіть за посиланням') }} <a style="color:#2264dc" href="{{ $activationUrl }}" target="_blank">{{ $activationUrl }} </a></p>

@stop
