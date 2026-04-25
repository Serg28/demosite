<div>
    <ul class="sub-menu">
        @foreach ($data as $contact)
            <li wire:key="contact-top-{{ $loop->index }}">
                <a href="tel:{{ $contact->t('value_languages') }}">
                    <span>{{ __t($contact->title) }}</span>{{ $contact->t('value_languages') }}</a>
            </li>
        @endforeach
    </ul>
</div>