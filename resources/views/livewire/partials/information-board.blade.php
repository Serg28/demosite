@php
    $information = \App\Models\InformationBoard::query()->where('is_active','=','1')->orderBy('priority')->first();
        $style = '';
        $class = 'baner fsz-13 text--center color--white fw-600 relative ';
        if(isset($information->style) && !empty($information?->t('style'))){
            $style .= $information->t('style');
        }
        if(isset($information->background) && !empty($information?->background)){
            $style .= 'background: url('. $information?->background .'); background-repeat: no-repeat; background-size: cover;';
        }else if(isset($information->color) && !empty($information?->color)){
            $style .= 'background: '. $information?->color.';' ;
        }


        if(isset($information->className) && !empty($information?->t('className'))){
            $class += $information->t('className');
        }
@endphp
@if (!empty($information))
<div class="{{$class}}" style="{{$style}}" >
    @if(isset($information->title) && !empty($information?->t('title'))) <p>{!! $information->t('title') !!}</p> @endif
    @if(isset($information->text) && !empty($information?->t('text'))) <p>{!! $information->t('text') !!}</p> @endif
    @if(isset($information->is_show_btn) && !empty($information->is_show_btn))  <div class="closer absolute flex v--center h--center"><img src="/assets/images/close-white.svg" alt="" class="fr-dii fr-draggable"></div> @endif
</div>
@endif