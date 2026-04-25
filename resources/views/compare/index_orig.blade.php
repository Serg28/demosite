@extends('layouts.default')

@section('seo_tags')
	<title>{{__t('Порівняння товарів')}}</title>
@stop

@section('main')
	@include('partials.breadcrumb_simple', ['page' => __t('Порівняння товарів')])

	<!-- END MAIN CONTENT -->
	<div class="main_content">
		<!-- START SECTION SHOP -->
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="compare_box">
							<div class="table-responsive">
                                <div>
                                    <p>
                                        @if (count($categories))
                                            @foreach($categories as $category)
                                                <a class="btn btn-fill-out" href="{{route('compare', ['category' => $category->id])}}"><span>{{ $category->t('title') }}</span></a>
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
								@if (count($products))
								<table class="table table-bordered text-center">
									<tbody>
									<tr class="pr_image">
										<td class="row_title">{{__t('Зображення товару') }}</td>
										@foreach($products as $product)
											<td class="row_img"><a href="{{$product->getUrl()}}">{!! $product->getImg(320, 350) !!}</a></td>
										@endforeach
									</tr>
									<tr class="pr_title">
										<td class="row_title">{{__t('Найменування товару') }}</td>
										@foreach($products as $product)
										<td class="product_name"><a href="{{$product->getUrl()}}">{{$product->t('title') }}</a></td>
										@endforeach
									</tr>
									<tr class="pr_price">
										<td class="row_title">{{__t('Ціна товару') }}</td>
										@foreach($products as $product)
										<td class="product_price">
											<span class="price">{{$product->getPrice()}} {{ setting('currency') }}</span>
											@if ($product->getPriceOld())
												<del>{{$product->getPriceOld()}} {{ setting('currency') }}</del>
												<div class="on_sale">
													<span>{{$product->getSale()}} {{__t('знижка')}}</span>
												</div>
											@endif
										</td>
										@endforeach
									</tr>
									<tr class="pr_rating">
										<td class="row_title">{{__t('Рейтинг') }}</td>
										@foreach($products as $product)
										<td>
											<div class="rating_wrap">
												<div class="rating">
													<div class="product_rate" style="width:{{$product->comment()->getRating()}}%"></div>
												</div>
												<span class="rating_num">({{$product->comment()->count()}})</span>
											</div>
										</td>
										@endforeach

									</tr>
									<tr class="pr_add_to_cart">
										<td class="row_title">{{__t('Додати до кошику')}}</td>
										@foreach($products as $product)
										<td class="row_btn"><a onclick="Basket.add(this)" data-count="1" data-id="{{$product->id}}" class="btn btn-fill-out"><i class="icon-basket-loaded"></i> {{__t('Купити') }}</a></td>
										@endforeach
									</tr>
									<tr class="description">
										<td class="row_title">{{__t('Опис товару')}}</td>
										@foreach($products as $product)
										<td class="row_text"><p>{{strip_tags($product->t('short_description'))}}</p></td>
										@endforeach
									</tr>
                                    @foreach($products[0]->characteristics as $characteristic)
                                    <tr class="compare">
                                        <td class="row_title">{{$characteristic->characteristic->t('title')}}</td>
										@foreach($products as $product)
											@if ($characteristicProduct = $product->characteristics()->where('characteristic_id', $characteristic->characteristic->id)->first())
												<td class="row_text">{{$characteristicProduct->characteristicOption->t('title')}}</td>
											@else
												<td class="row_text"> - </td>
											@endif
										@endforeach
                                    </tr>
                                    @endforeach
									<tr class="pr_remove">
										<td class="row_title"></td>
										@foreach($products as $product)
										<td class="row_remove">
											<a style="cursor: pointer" onclick="Compare.doToggle(this, {{$product->id}}); setInterval('location.href=location.href', 500) " class="active"><span>{{__t('Видалити')}}</span> <i class="fa fa-times"></i></a>
										</td>
										@endforeach
									</tr>
									</tbody>
								</table>
								@else
									<p style="padding: 50px 0; text-align: center">{{__t('Відсутні товари для порівняння')}}</p>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END SECTION SHOP -->

	</div>
	<!-- END MAIN CONTENT -->
@stop
<script>
    import All_header1 from "../../../public/packages/vis/builder/js/all_header1.js";
    export default {
        components: {All_header1}
    }
</script>
