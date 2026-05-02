<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // @money($value) — smart: 0 or 2 decimals based on value
        // @money($value, 0) — forced decimals
        Blade::directive('money', function (string $expression): string {
            // Split at compile time so second arg is decimals, not explode limit
            $parts     = array_map('trim', explode(',', $expression, 2));
            $valueExpr = $parts[0];

            if (isset($parts[1])) {
                $decimalsExpr = $parts[1];

                return "<?php echo number_format((float)({$valueExpr}), (int)({$decimalsExpr}), ',', '&nbsp;'); ?>";
            }

            return <<<PHP
            <?php
                \$_money_val = (float)({$valueExpr});
                \$_money_dec = ((int)(\$_money_val * 100) % 100) ? 2 : 0;
                echo number_format(\$_money_val, \$_money_dec, ',', '&nbsp;');
                unset(\$_money_val, \$_money_dec);
            ?>
            PHP;
        });

        Blade::directive('loop', function (string $expression): string {
            return "<?php foreach ($expression): ?>";
        });

        Blade::directive('endloop', function (): string {
            return '<?php endforeach; ?>';
        });

        Blade::if('notLighthouse', function (): bool {
            return once(static function (): bool {
                $userAgent = request()->header('User-Agent', '');

                return stripos($userAgent, 'Lighthouse') === false
                    && stripos($userAgent, 'Speed Insights') === false;
            });
        });
    }
}
