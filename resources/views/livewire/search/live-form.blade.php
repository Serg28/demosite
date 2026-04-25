<div class="search" x-data="{ open: false, text: $wire.entangle.text }" @click.outside="open = false" @keyup.escape.window="open = false">
    <form wire:submit="submit" id="{{$formId}}" autocomplete="off" class="search-form relative" action="{{route('search-result.page')}}" method="get">
        @csrf
        @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
            <div class="visible flex v--center pt-8 pb-8">
                <button id="{{$formId}}_voice_trigger" type="button" class="voice-search"><img src="/assets/images/voice.svg" alt="{{__t('Голосовий пошук')}}"></button>
                <input id="{{$formId}}_search_field" type="text"
                     class="@error('text') error @enderror fsz-16"
                     @if(!empty($text)) @focus="open = true" @endif
                     @input="open = true"
                     wire:model.live.debounce.500ms="text"
                     @keydown.enter="$wire.set('text', $event.target.value)"
                     name="text" placeholder="{{__t('Пошук товарів')}}"
                     value="{{$text}}"
                >

                <div class="closer input-placeholder-clear" @click="open=false; $wire.text = ''"><img src="/assets/images/closer.svg" alt=""></div>

                @if(empty($text))
                    @include('livewire.search.partials.default-popup')
                @else
                    @include('livewire.search.partials.result-popup')
                @endif

            </div>
    </form>
</div>

@script
<script>
    document.addEventListener('livewire:navigated', () => {
    let voiceTrigger = document.getElementById("{{$formId}}_voice_trigger");
    let searchForm = document.getElementById("{{$formId}}");
    let searchInput = document.getElementById("{{$formId}}_search_field");
    let result = document.getElementById("result");

    window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    let recognition;

    if (window.SpeechRecognition) {
        recognition = new window.webkitSpeechRecognition();
        recognition.interimResults = true;
        recognition.lang = 'ru-RU';
        recognition.addEventListener('result', transcriptHandler);

        recognition.onerror = function(event) {
            if (event.error === 'no-speech') {
                voiceTrigger.classList.remove('active');
                searchInput.setAttribute("placeholder", "Поиск...");
            }
        };
    } else {
        voiceTrigger.parentNode.removeChild(voiceTrigger);
    }

    voiceTrigger.addEventListener('click', listenStart);
    voiceTrigger.addEventListener('touchstart', listenStart);

    function listenStart(e) {
        e.preventDefault();
        searchInput.setAttribute("placeholder", "Говорите...");
        voiceTrigger.classList.add('active');
        recognition.start();
    }

    function parseTranscript(e) {
        return Array.from(e.results)
            .map(result => result[0])
            .map(result => result.transcript)
            .join('');
    }

    function transcriptHandler(e) {
        searchInput.value = parseTranscript(e);
        if (e.results[0].isFinal) {
           $wire.text = parseTranscript(e);
           $wire.submit();
        }
    }
    });
</script>

@endscript
