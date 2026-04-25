@extends('layouts.mail')

@section('main')
	<table align="center" border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="100%" class="table-content" style="width: 100%;max-width: 640px;min-width: 320px;border-collapse: collapse;border-spacing: 0;margin: 0 auto;padding: 0;text-align: center;mso-table-lspace: 0px;mso-table-rspace: 0px;">
		<tbody>
		<tr>
			<td class="table-content__title" style="margin: 0;padding: 20px 0px;font-size: 32px;color: #282c2c;font-weight: 700;">
				<p style="margin: 0;padding: 20px 0px;font-size: 32px;color: #282c2c;font-weight: 700; text-align: center;">Ви просили повідомити вас коли <a style="color: #e60000;" href="{{$product->getUrl()}}">{{$product->t('title')}}</a> з'явиться у наявності</p>
			</td>
		</tr>
		<tr>
			<td class="table-content__message" style="margin: 0;padding: 20px 0px;font-size: 16px;color: #6b738a;">
				<p>Зараз вартість товару: {{$product->price}}</p>
			</td>
		</tr>
		</tbody>
	</table>
@stop
