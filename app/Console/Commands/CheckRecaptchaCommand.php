<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
//use App\Mail\RecaptchaDisabled;

class CheckRecaptchaCommand extends Command
{
    protected $signature = 'recaptcha:check';

    protected $description = 'Check the validity of reCAPTCHA secret key';

    public function handle()
    {
        $secret = config('recaptcha.api_secret_key');
        $response = Http::get("https://www.google.com/recaptcha/api/siteverify?secret={$secret}");

        $responseData = $response->json();

        if ((bool)$responseData['success'] == false && in_array('invalid-input-secret', $responseData['error-codes'])) {
            // Отключаем капчу в .env
            file_put_contents('.env', str_replace(
                'RECAPTCHA_ENABLED=true',
                'RECAPTCHA_ENABLED=false',
                file_get_contents('.env')
            ));

            // Сбрасываем кеш конфигурации
            Artisan::call('config:clear');

            // Уведомляем админа
            //$adminEmail = 'адрес_админа@example.com';
            //Mail::to($adminEmail)->send(new RecaptchaDisabled());

            $this->error('reCAPTCHA secret key is invalid. Captcha has been disabled.');
        } else {
            $this->info('reCAPTCHA secret key is valid.');
        }
    }
}
