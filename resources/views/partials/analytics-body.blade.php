{{-- GTM noscript (body, одразу після <body>) --}}
@if(setting('checkbox_google_tag_manager'))
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ setting('code_google_tag_manager') }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif
