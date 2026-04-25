@extends('layouts.mail')

@section('main')
	<table align="center" border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="100%" class="table-content" style="width: 100%;max-width: 640px;min-width: 320px;border-collapse: collapse;border-spacing: 0;margin: 0 auto;padding: 0;text-align: center;mso-table-lspace: 0px;mso-table-rspace: 0px;">
		<tbody>
		<tr>
			<td class="table-content__message" style="margin: 0;padding: 20px 0px;font-size: 16px;color: #6b738a;">
				Имя: <strong>{{$order->name}}</strong>
			</td>
		</tr>
		<tr>
			<td class="table-content__message" style="margin: 0;padding: 20px 0px;font-size: 16px;color: #6b738a;">
				Телефон: <strong>{{$order->phone}}</strong>
			</td>
		</tr>

		<tr>
			<td class="table-content__message" style="margin: 0;padding: 20px 0px;font-size: 16px;color: #6b738a;">
				Email: <strong>{{$order->email}}</strong>
			</td>
		</tr>
        <tr>
            <td class="table-content__message" style="margin: 0;padding: 20px 0px;font-size: 16px;color: #6b738a;">
                Товар: <strong>{{$order->product->t('title')}}</strong>
            </td>
        </tr>

		</tbody>
	</table>
@stop
