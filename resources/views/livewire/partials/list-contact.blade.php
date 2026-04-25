<div>
    <div class="flex-row-wrap">
        @foreach ($data as $contact)

            <div wire:key="list-contact-{{ $loop->index }}" class="flex-row">
                <p>{{ __t($contact->title) }}</p>
                <a href="tel:{{ $contact->t('value_languages') }}">{{ $contact->t('value_languages') }}</a>
            </div>
        @endforeach
    </div>
</div>
