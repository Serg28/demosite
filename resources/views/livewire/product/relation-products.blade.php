<div class="columns flex v--start h--between mt-16" style="flex-wrap: wrap; gap: 15px;">
    @if ($relationProducts)
        @loop($relationProducts as $title => $items)
            @if($this->characteristics[$title]=='color')
                <div class="column flex fd--column">
                    <p class="fsz-14 mb-12">{{$title}}:</p>
                    <div class="colors flex v--center pt-3">
                        @foreach($items as $k=>$item)
                            <a href="{{$item['product']->getUrl()}}" wire:navigate.hover  class="color product-card-color
                            {{$item['product']->quantity < 0 ? 'disabled' : ''}} {{$item['product']->id == $page->id ? 'current' : ''}}"
                               style="background: {{$item['color'] ?? ''}};" data-color="{{$k}}" title="{{ e($item['title']) }}">
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="column">
                    <p class="fsz-14 mb-12">{{$title}}:</p>
                    <div class="labels flex v--center">
                        @foreach($items as $item)
                            <a href="{{$item['product']->getUrl()}}" wire:navigate.hover class="label flev v--center h--center fsz-14 pt-4 pb-4 pl-8 pr-8
                            {{$item['product']->quantity < 0 ? 'disabled' : ''}}
                            {{$item['product']->id == $page->id ? 'current' : ''}}">{!! $item['title'] !!}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endloop
    @endif
</div>
