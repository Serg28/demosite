@extends('layouts.mail')

@section('main')
    <p>{{__t('Приветствуем')}}.</p>
    <p>{!! str_replace(['[sitename]', '[link]'], [ucfirst(config('app.name')), '<a href="'.$comment->product->getUrl().'#id' . $comment->id. '" target="_blank">'.$comment->product->getUrl().'#id' . $comment->id. '</a>'], __t('Ви отримали відповідь на залишений коментар на сайті [sitename]. Щоб переглянути його перейдіть за наступним посиланням - [link]')) !!}</p>
@stop
