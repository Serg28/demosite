<!-- START SECTION BREADCRUMB -->
<div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
    <div class="container">
        <ul class="flex v--center" itemscope itemtype="https://schema.org/BreadcrumbList">
            @foreach ($breadcrumbs->crumbs as $key=>$item)
                @if ($loop->last)
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span title="{{ $item['title'] }}" itemprop="item" class="fsz-14 color--gray">
                            <span itemprop="name">{{ $item['title'] }}</span>
                        </span>
                        <meta itemprop="position" content="{{ ++$key }}"/>
                    </li>
                @else
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a itemprop="name" href="{{ $item['url'] }}" title="{{ $item['title'] }}" class="flex color--black fsz-14 v--center">
                            {{ $item['title'] }}
                            <img src="/assets/images/br-arrow-right.svg" alt="arrow">
                        </a>
                        <meta itemprop="position" content="{{ ++$key }}"/>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>