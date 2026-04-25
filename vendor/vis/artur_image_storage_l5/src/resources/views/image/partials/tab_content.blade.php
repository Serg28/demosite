<img {{isset($info) && !$info['default_tab'] ? 'real-':''}}src="{{asset($entity->getSource($ident))}}" class="superbox-current-img">
