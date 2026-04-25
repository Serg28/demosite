<div class="row">
	<article class="col-sm-12 col-md-12 col-lg-6">
		<div id="wid-id-2" class="jarviswidget  jarviswidget-color-blueDark jarviswidget-sortable" data-widget-fullscreenbutton="false" data-widget-editbutton="false" style="" role="widget">
			<header>
				<h2>{{__cms('Заказов на сумму')}}</h2>
				<ul id="widget-tab-1" class="nav nav-tabs pull-right">
					<li class="active">
						<a data-toggle="tab" href="#cost_today"> <span class="hidden-mobile hidden-tablet"> {{__cms('За сегодня')}} </span> </a>

					</li>
					<li>
						<a data-toggle="tab" href="#cost_all"> <span class="hidden-mobile hidden-tablet"> {{__cms('За период')}} </span> </a>
					</li>
					<li>
						<a data-toggle="tab" href="#cost_avg"> <span class="hidden-mobile hidden-tablet"> {{__cms('Средний чек')}}</span></a>
					</li>
				</ul>
			</header>
			<div role="content">
				<div class="tab-content padding-5">
					<div class="tab-pane fade in active" id="cost_today">
						<div style="text-align: center; font-size: 20px; padding: 5px 0 10px 0;">{{number_format(round($costToday))}} грн.</div>
					</div>
					<div class="tab-pane fade in" id="cost_all">
                        <div style="text-align: right">
                            <label class="input" style="width: 110px; position: relative">
                                <input type="text"
                                       autocomplete="off"
                                       id="datepicker_sum_from"
                                       class="form-control datepicker datepicker_sum"
                                       name="trend_from"
                                       value="{{ $from ? $from->created_at->format('Y-m-d') : '' }}"
                                >
                                <i class="fa fa-calendar" style="position: absolute; top: 10px; right: 10px"></i>
                            </label>
                            <label class="input" style="width: 110px; position: relative">
                                <input type="text"
                                       autocomplete="off"
                                       id="datepicker_sum_to"
                                       class="form-control datepicker datepicker_sum"
                                       name="trend_to"
                                       value="{{ $to ? $to->created_at->format('Y-m-d') : '' }}"
                                >
                                <i class="fa fa-calendar" style="position: absolute; top: 10px; right: 10px"></i>
                            </label>
                        </div>
						<div style="text-align: center; font-size: 20px; padding: 5px 0 10px 0;" id="new_datepicker_sum">{{number_format(round($totalCost))}} грн.</div>
					</div>
					<div class="tab-pane fade" id="cost_avg">
                        <div style="text-align: right">
                            <label class="input" style="width: 110px; position: relative">
                                <input type="text"
                                       autocomplete="off"
                                       id="datepicker_avg_from"
                                       class="form-control datepicker datepicker_avg"
                                       name="trend_from"
                                       value="{{ $from ? $from->created_at->format('Y-m-d') : '' }}"
                                >
                                <i class="fa fa-calendar" style="position: absolute; top: 10px; right: 10px"></i>
                            </label>
                            <label class="input" style="width: 110px; position: relative">
                                <input type="text"
                                       autocomplete="off"
                                       id="datepicker_avg_to"
                                       class="form-control datepicker datepicker_avg"
                                       name="trend_to"
                                       value="{{ $to ? $to->created_at->format('Y-m-d') : '' }}"
                                >
                                <i class="fa fa-calendar" style="position: absolute; top: 10px; right: 10px"></i>
                            </label>
						<div style="text-align: center; font-size: 20px; padding: 5px 0 10px 0;" id="new_datepicker_avg">{{number_format(round($avgCost))}} грн.</div>
					</div>
				</div>
			</div>
		</div>
		</div>

		<div id="wid-id-2" class="jarviswidget  jarviswidget-color-blueDark jarviswidget-sortable" data-widget-fullscreenbutton="false" data-widget-editbutton="false" style="margin-top: 20px" role="widget">
			<header>
				<h2>{{__cms('Последние 5 заказов')}}</h2>
			</header>
			<div role="content">
				<div class="widget-body no-padding">
					<table id="datatable_fixed_column" class="table table-hover table-bordered">
						<thead>
						<tr>
							<th>#</th>
							<th>{{__cms('Имя')}}</th>
							<th>{{__cms('Телефон')}}</th>
							<th>{{__cms('Товары')}}</th>
							<th>{{__cms('Сумма, грн')}}</th>
							<th>{{__cms('Дата/время')}}</th>
						</tr>
						</thead>
						<tbody>
						@forelse($lastOrder as $order)
							<tr>
								<td><a href="/admin/orders?id={{$order->id}}" target="_blank">{{$order->id}}</a></td>
								<td>{{$order->name}}</td>
								<td>{{$order->phone}}</td>
								<td>
									@foreach($order->products as $product)
											<a href="{{$product->product->getUrl()}}" >{{$product->product->t('title')}}</a><br>
									@endforeach
								</td>
								<td>{{$order->cost}}</td>
								<td>{{$order->created_at}}</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" style="padding: 20px">{{__cms('Пока нет заказов')}}</td>
							</tr>
						@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="wid-id-2" class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" data-widget-fullscreenbutton="false" data-widget-editbutton="false"  role="widget" style="margin-top: 20px">
			<header>
				<h2>{{__cms('Поисковые запросы')}}</h2>
				<ul id="widget-tab-1" class="nav nav-tabs pull-right">
					<li class="active">
						<a data-toggle="tab" href="#search_last"> <span class="hidden-mobile hidden-tablet"> {{__cms('Последние')}} </span> </a>

					</li>
					<li>
						<a data-toggle="tab" href="#search_top"> <span class="hidden-mobile hidden-tablet"> {{__cms('Топ 5')}} </span></a>
					</li>
				</ul>
			</header>
			<div role="content">
				<div class="widget-body no-padding">
					<div class="tab-content padding-5">
						<div class="tab-pane fade in active" id="search_last">
							<table id="datatable_fixed_column" class="table table-hover table-bordered">
								<thead>
								<tr>
									<th>{{__cms('Фраза')}}</th>
									<th>{{__cms('Пользователь')}}</th>
									<th>{{__cms('Дата/Время')}}</th>
								</tr>
								</thead>
								<tbody>
								@forelse($lastSearchQuery as $searchInfo)
									<tr>
										<td>{{$searchInfo->query}}</td>
										<td>{!! $searchInfo->getUserFullName() !!}</td>
										<td>{{$searchInfo->created_at}}</td>
									</tr>
								@empty
									<tr>
										<td colspan="3" style="padding: 20px">{{__cms('Пока нет запросов')}}</td>
									</tr>
								@endforelse
								</tbody>
							</table>

						</div>
						<div class="tab-pane fade" id="search_top">
							<table id="datatable_fixed_column" class="table table-hover table-bordered">
								<thead>
								<tr>
									<th>{{__cms('Фраза')}}</th>
									<th>{{__cms('Количество')}}</th>

								</tr>
								</thead>
								<tbody>
								@forelse($topSearch as $searchInfo)
									<tr>
										<td>{{$searchInfo->query}}</td>
										<td>{{$searchInfo->count_search}}</td>
									</tr>
								@empty
									<tr>
										<td colspan="3" style="padding: 20px">{{__cms('Пока нет запросов')}}</td>
									</tr>
								@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
    </article>

	<article class="col-sm-12 col-md-12 col-lg-6">

		<div id="wid-id-2" class="jarviswidget  jarviswidget-color-blueDark jarviswidget-sortable" data-widget-fullscreenbutton="false" data-widget-editbutton="false" style="" role="widget">
			<header>
				<h2>{{__cms('Новые пользователи')}}</h2>
			</header>
			<div role="content">
				<div class="widget-body no-padding">
					<table id="datatable_fixed_column" class="table table-hover table-bordered">
						<thead>
						<tr>
							<th>#</th>
							<th>{{__cms('ФИО')}}</th>
							<th>Email</th>
							<th>{{__cms('Дата регистрации')}}</th>
						</tr>
						</thead>
						<tbody>
						@forelse($lastUsers as $user)
							<tr>
								<td>{{$user->id}}</td>
								<td>
									<a href="/admin/users?id={{$user->id}}" target="_blank">{{$user->getFullName()}}</a>
								</td>
								<td>{{$user->email}}</td>
								<td>{{$user->created_at}}</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" style="padding: 20px">{{__cms('Пока нет пользователей')}}</td>
							</tr>
						@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="wid-id-2" class="jarviswidget  jarviswidget-color-blueDark jarviswidget-sortable" data-widget-fullscreenbutton="false" data-widget-editbutton="false" style="margin-top: 20px" role="widget">
			<header>
				<h2>{{__cms('Последнии комментарии')}}</h2>
			</header>
			<div role="content">
				<div class="widget-body no-padding">
					<table id="datatable_fixed_column" class="table table-hover table-bordered">
						<thead>
						<tr>
							<th>#</th>
							<th>{{__cms('ФИО')}}</th>
							<th>{{__cms('Сообщение')}}</th>
							<th>{{__cms('Страница')}}</th>
							<th>{{__cms('Дата/время')}}</th>
						</tr>
						</thead>
						<tbody>
						@forelse($lastComments as $comment)
							<tr>
								<td>{{$comment->id}}</td>
								<td>
									@if ($comment->user_id)
										<a href="/admin/users?id={{$comment->user_id}}" target="_blank">{{$comment->name}}</a>
									@else
										{{$comment->name}}
									@endif
								</td>
								<td><a href="{{$comment->commentable?->getUrl()}}" target="_blank">{{Str::limit($comment->body, 50)}}</a></td>
								{{--<td>
									<a href="{{$comment->commentable?->getUrl()}}" target="_blank">{{$comment->commentable?->t('title')}}</a>
								</td> --}}
								<td>{{$comment->created_at}}</td>
							</tr>
						@empty
							<tr>
								<td colspan="5" style="padding: 20px">{{__cms('Пока нет комментариев')}}</td>
							</tr>
						@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>

        <div id="wid-id-2" class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" data-widget-fullscreenbutton="false" data-widget-editbutton="false"  role="widget" style="margin-top: 20px">
            <header>
                <h2>{{__cms('ТОП 5 товаров')}}</h2>
                <ul id="widget-tab-1" class="nav nav-tabs pull-right">
                    <li class="active">
                        <a data-toggle="tab" href="#most_viewed"> <span class="hidden-mobile hidden-tablet"> {{__cms('Просматриваемых')}} </span> </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#most_ordered"> <span class="hidden-mobile hidden-tablet"> {{__cms('Заказываемых')}} </span></a>
                    </li>
                </ul>
            </header>
            <div role="content">
                <div class="widget-body no-padding">
                    <div class="tab-content padding-5">
                        <div class="tab-pane fade in active" id="most_viewed">
                            <table id="datatable_fixed_column" class="table table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th>{{__cms('Товар')}}</th>
                                    <th>{{__cms('Кол-во')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($views as $view)
                                    <tr>
                                        <td><a href="{{ $view->getUrl() }}" target="_blank">{{$view->t('title')}}</a></td>
                                        <td>{{$view->count_views}}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="padding: 20px">{{__cms('Пока нет запросов')}}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>

                        </div>
                        <div class="tab-pane fade" id="most_ordered">
                            <table id="datatable_fixed_column" class="table table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th>{{__cms('Товар')}}</th>
                                    <th>{{__cms('Кол-во')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td><a href="{{ $order->product->getUrl() }}" target="_blank">{{ $order->product->t('title') }}</a></td>
                                        <td>{{$order->count_order}}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="padding: 20px">{{__cms('Пока нет запросов')}}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </article>
</div>
<script>
    $("title").text("{{__cms('Рабочий стол')}}");

    $( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        nextText: ">",
        prevText: "<"
    });

    $('.datepicker_sum').on('change', function () {
        getCountValue(
            $('#datepicker_sum_from').val(),
            $('#datepicker_sum_to').val(),
            $('#new_datepicker_sum'),
            'sum'
        );
    });

    $('.datepicker_avg').on('change', function () {
        getCountValue(
            $('#datepicker_avg_from').val(),
            $('#datepicker_avg_to').val(),
            $('#new_datepicker_avg'),
            'avg'
        );
    });

    function getCountValue(start, end, price, method) {
        $.ajax({
            method: "POST",
            url:  '/admin/get-order/price-by-date',
            data: { from: start, to: end, method: method},
            success: function(result){
                price.html(result);
            }
        })
    }
</script>

