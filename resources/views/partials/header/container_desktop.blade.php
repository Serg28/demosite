@include('livewire.partials.information-board')
<div class="container">
    <div class="top-header-row flex v--center pt-16 pb-16">
        @include('partials.header.logo')
       <livewire:partials.topmenu />
       <livewire:partials.langmenu :page="$page??null" />
    </div>
</div>
