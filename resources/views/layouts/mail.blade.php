<!DOCTYPE html
		PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<style>
	a {
		color:#2264dc
	}
	td {
        word-wrap:break-word;
    }
    .wrap-td-content {
        font-size: 16px!important;
    }
	@media (max-width: 500px) {
		.table-content__title{
			font-size: 28px!important;
		}
		.table-content__message{
			font-size: 16px!important;
		}
		.table-content__info p{
			font-size: 16px!important;
		}
		.table-content__info span{
			font-size: 16px!important;
		}
		.only-dt{
			display: none!important;
		}
		.only-mb{
			display: table!important;
		}
		.contacts-information__email, .contacts-information__adress, .contacts-information__phone{
			text-align: center!important;
		}
		.footer-description td, .footer-description td a{
			font-size: 14px!important;
		}
	}
</style>

<body style="padding: 20px 0px;background-color: #ffffff;margin: 0 auto;font-family: Roboto, Verdana, sans-serif;">
<table align="center" border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="100%" class="wrap-table-email" style="margin: 0 auto;max-width: 940px; min-width: 320px;">
	<tbody>
	<tr>
		<td class="wrap-td-header" style="margin: 0;padding: 20px 10px 14px 10px;background-color:#ffffff">
			<table align="center" border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="100%" class="table-header" style="width: 100%;max-width: 940px;min-width: 320px;border-collapse: collapse;border-spacing: 0;margin: 0 auto;padding: 0;text-align: center;mso-table-lspace: 0px;mso-table-rspace: 0px;">
				<tbody>
				<tr>
					<td class="td-header" style="margin: 0;padding: 0;">
                        <img src="{{asset(setting('logo_for_letter'))}}" style="max-height: 40px" alt="{{config('app.name')}}" />
					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td class="wrap-td-content" style="margin: 0;padding: 20px 10px;font-size:16px">
			@yield('main')
            <p>
                {{__t('С уважением')}},<br>
                {{__t('Команда')}} {{config('app.name')}}<br>
                @if(setting('email-v-futere')){{setting('email-v-futere')}}<br>@endif
                @if(setting('telefon-v-pismah'))<a href="tel:{{setting('telefon-v-pismah')}}">{{setting('telefon-v-pismah')}}</a><br>@endif
			</p>
			<p>
                @if(setting('footer_address'))<a href="#">{{setting('footer_address')}}</a><br>@endif
                @if(setting('link-to-facebook'))<a href="{{setting('link-to-facebook')}}" target="_blank"><img srcset="{{getUrl('/')}}storage/editor/fotos/24x24/7c003ed44a938a734c8aaffc9f3de454_1707409180.png" src="{{getUrl('/')}}storage/editor/fotos/24x24/7c003ed44a938a734c8aaffc9f3de454_1707409180.png" alt="Facebook" style="width: 26px; height: 26px; border-radius: 8px;margin: 5px;border: 1px solid #d1d8e3;padding: 2px;"></a>@endif
                @if(setting('link-to-instagram'))<a href="{{setting('link-to-instagram')}}" target="_blank"><img srcset="{{getUrl('/')}}storage/editor/fotos/24x24/3abf16a75b19a5ac8a9264aefabf9a77_1707409105.png" src="{{getUrl('/')}}storage/editor/fotos/24x24/3abf16a75b19a5ac8a9264aefabf9a77_1707409105.png" alt="Instagram" style="width: 26px; height: 26px; border-radius: 8px;margin: 5px;border: 1px solid #d1d8e3;padding: 2px;"></a>@endif
                @if(setting('link-to-telegram'))<a href="{{setting('link-to-telegram')}}" target="_blank"><img srcset="{{getUrl('/')}}assets/images/telegram.png" src="{{getUrl('/')}}assets/images/telegram.png" alt="Telegram" style="width: 26px; height: 26px; border-radius: 8px;margin: 5px;border: 1px solid #d1d8e3;padding: 2px;"></a>@endif
				@if(setting('link-to-viber'))<a href="{{setting('link-to-viber')}}" target="_blank"><img srcset="{{getUrl('/')}}assets/images/viber.png" src="{{getUrl('/')}}assets/images/viber.png" alt="Viber" style="width: 26px; height: 26px; border-radius: 8px;margin: 5px;border: 1px solid #d1d8e3;padding: 2px;"></a>@endif
            </p>
		</td>
	</tr>


    {{--<tr>
        <td class="wrap-td-header" style="margin: 0;padding: 20px 10px 14px 10px;background-color:#21233D">
            <table align="center" border="0" bgcolor="#21233D" cellpadding="0" cellspacing="0" width="100%" class="table-header" style="width: 100%;max-width: 940px;min-width: 320px;border-collapse: collapse;border-spacing: 0;margin: 0 auto;padding: 0;text-align: center;mso-table-lspace: 0px;mso-table-rspace: 0px;">
                <tbody>
                <tr>
                    <td class="td-header" style="margin: 0;padding: 0;">
                        <img src="{{asset(setting('logo_for_letter'))}}" style="height: 40px;width: 231px;" alt="{{env('APP_NAME')}}" />
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>--}}
	</tbody>
</table>

</body>

</html>
