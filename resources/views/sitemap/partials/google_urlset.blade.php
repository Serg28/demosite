<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach($urls as $item)
        @if($item->getUrl())
            <url>
                <loc>{{ $item->getUrl() }}</loc>
                <changefreq>{{$model['changefreq']}}</changefreq>
                <lastmod>
                    {{ $item->updated_at ? $item->updated_at->format('Y-m-d\TH:i:sP') : now()->format('Y-m-d\TH:i:sP') }}
                </lastmod>
                <priority>{{$model['priority']}}</priority>
            </url>
        @endif
    @endforeach
</urlset>
