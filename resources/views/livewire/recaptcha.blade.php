<span>
    <input type="hidden" name="g_recaptcha_response" id="{{$hiddenInput}}">
</span>

@script
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('livewire:init', () => {
            Livewire.on('recaptcha-initialized', (params) => {
                initCaptcha(params.formId, params.hiddenInput);
            });
        });
    });
</script>
@endscript

@push('footer-scripts')
    @if(config('recaptcha.active'))
    <script defer src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
    @endif
@endpush
