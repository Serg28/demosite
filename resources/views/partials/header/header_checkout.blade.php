<header class="header checkout-header">
    <div class="container">
        <div class="top-header-row flex v--center h--between pl-20 pr-20 pt-16 pb-16">
            @include('partials.header.logo')
            @include('partials.tel_info', ['check'=> true])
            @include('partials.tel_info', ['mobil' => true])
        </div>
    </div>
</header>