@extends('layouts.mail')

@section('main')

	<p>{{__t('Вітаємо Вас')}}, {{ $user->first_name }}!</p>
	<p>{!! str_replace(['[sitename]','[url]'], [config('app.name'), getUrl('/')], __t('Ви запросили новий пароль для входу до свого облікового запису на сайті <a href="[url]" style="color:#2264dc">[sitename]</a>')) !!}.</p>
	<p>{{ __t('Ваш новий пароль') }}: <strong>{{$newPassword}}</strong></p>
	<p>{{ __t('Після входу до облікового запису Ви зможете його змінити') }}.</p>

	<p>{{ __t('Якщо цей лист прийшов Вам помилково, просто проігноруйте його.') }}</p>
@stop