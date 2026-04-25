@extends('layouts.app')

@section('content')
<div class="catalog-container py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">{{ $category->title['ua'] ?? $category->title['ru'] ?? '' }}</h1>

        <div class="flex gap-8">
            <!-- Sidebar with Facets -->
            <aside class="w-64 flex-shrink-0">
                @livewire('catalog.facets', ['category' => $category])
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                <!-- Sort Bar -->
                @livewire('catalog.sort-bar')

                <!-- Product List -->
                @livewire('catalog.product-list', ['category' => $category])
            </main>
        </div>
    </div>
</div>
@endsection
