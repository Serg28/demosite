<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Linecore\Cms\User as CmsUser;

/**
 * Модель користувача магазину.
 * Успадковує CmsUser (Linecore\Cms\User → EloquentUser) для повної сумісності з Sentinel:
 * inRole(), hasAccess(), groups() — доступні без зміни auth-потоку.
 * Реалізує Authenticatable + CanResetPassword для роботи з Fortify.
 */
class User extends CmsUser implements Authenticatable, CanResetPassword
{
    use AuthenticatableTrait;
    use CanResetPasswordTrait;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'patronymic',
        'phone',
        'email',
        'password',
        'permissions',
        'discount_cumulative',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // Без 'password' => 'hashed' — всі реєстрації через Hash::make() явно.
            // Sentinel NativeHasher (bcrypt) сумісний з Hash::check().
        ];
        // 'permissions' => 'json' успадковується з EloquentUser::$casts
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(UserContact::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserContact::class)->where('type', 'address');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(UserContact::class)->where('type', 'recipient');
    }

    public function discountCards(): HasMany
    {
        return $this->hasMany(DiscountCard::class);
    }

    public function providers(): HasMany
    {
        return $this->hasMany(UserProvider::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * ID товарів у списку бажань користувача.
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    public function favoriteProductIds(): \Illuminate\Support\Collection
    {
        return $this->wishlists()->pluck('product_id');
    }

    /**
     * Накопичувальна знижка користувача на всі товари (%).
     * Оновлюється OrderObserver при виконанні замовлень.
     */
    public function effectiveDiscount(): int
    {
        return $this->discount_cumulative ?? 0;
    }

    public function hasPassword(): bool
    {
        return ! empty($this->password);
    }

    /**
     * Чи підключено Fortify 2FA (legacy — поле залишається в таблиці).
     * Використовується Security компонентом.
     */
    public function hasTwoFactor(): bool
    {
        return ! empty($this->two_factor_confirmed_at);
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
