<?php

namespace App\Models;

use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Vis\Builder\Helpers\Traits\Rememberable;
use Vis\Builder\User as UserBuilder;

class User extends UserBuilder
{
    use Rememberable;

    protected $guarded = [];

    protected $hidden = ['password'];

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'patronymic',
        'phone',
        'password'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'birth',
    ];


    public function getAuthIdentifier(): int|string|null
    {
        return auth()->id();
    }

    public function getCreatedAtAttribute($value): string
    {
        return $this->formatDate($value);
    }

    public function getUpdatedAtAttribute($value): string
    {
        return $this->formatDate($value);
    }

    public function getBirthAttribute($value): string
    {
        return $this->formatDate($value);
    }

    public function getIsActivatedAttribute(): bool
    {
        $activation = Activation::completed($this);
        return $activation!==false;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function unfinishedBasket(): HasMany
    {
        return $this->hasMany(UnfinishedBasket::class)->orderByDesc('created_at');
    }

    public function getUrlCms(): string
    {
        return '/admin/users?id='.$this->id;
    }

    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function discountcard(): hasOne
    {
        return $this->hasOne(DiscountCard::class, 'id', 'phone');
    }

    public function isNew() {
        return Carbon::now()< Carbon::parse($this->created_at)->addMinutes(5);
    }

    public function getImgPath($width = '', $height = ''): bool|string|null
    {
        $picture = $this->image;

        if (! $picture) {
            $picture = '/img/pi1.png';
        }

        $size = [];
        if ($width) {
            $size['w'] = $width;
        }

        if ($height) {
            $size['h'] = $height;
        }

        return  glide($picture, $size);
    }

    /**
     * Проверяет, имеет ли пользователь все указанные разрешения.
     *
     * @param  array  $permissions Массив с именами разрешений, которые нужно проверить.
     * @return bool Возвращает true, если пользователь имеет все указанные разрешения, иначе false.
     *
     * @example
     * ```
     * $user->hasPermissions(["orders.view", "orders.update", "orders.save"]);
     * $user->hasPermissions(["orders.*"]); //подразумевает все права раздела прав orders
     * ```
     */
    /*public function hasPermissions(array $permissions): bool
    {
        return $this->whereHas('groups', function ($query) use ($permissions) {
            foreach ($permissions as $permission) {
                $query->whereJsonContains('permissions', [$permission => true]);
            }
        })->exists();
    }*/
    public function hasPermissions(array $permissions): bool
    {
        return $this->hasAccess($permissions);
    }

    /**
     * Проверяет, имеет ли пользователь любую из указанных разрешений.
     *
     * @param  array  $permissions Массив с именами разрешений, которые нужно проверить.
     * @return bool Возвращает true, если пользователь имеет все указанные разрешения, иначе false.
     *
     * @example
     * ```
     * $user->hasAnyPermissions(["orders.view", "orders.update", "orders.save"]);
     * $user->hasAnyPermissions(["orders.*"]); //подразумевает все права раздела прав orders
     * ```
     */
    public function hasAnyPermissions(array $permissions): bool
    {
        return $this->hasAnyAccess($permissions);
    }

    //Scope для выборки только юзеров с правом доступа к заказам
    public function scopeHasOrderAccess($query)
    {
        $permissions = ["orders.view", "orders.update", "orders.save"];
        return $query->with('groups')->whereHas('groups', function ($query) use ($permissions) {
            foreach ($permissions as $permission) {
                $query->whereJsonContains('permissions', [$permission => true]);
            }
        });
    }

    /**
     * Проверяет, находится ли пользователь в одной из групп с заданными слагами.
     *
     * @param array $slugs
     * @return bool
     */
    public function inGroups(array $slugs): bool
    {
        return $this->groups->contains(fn($group) => in_array($group->slug, $slugs));
    }

    protected function formatDate($value, $format = 'Y-m-d H:i:s', $timezone = 'Europe/Kiev'): string
    {
        return $value ? Carbon::parse($value)->timezone($timezone)->format($format) : '';
    }
}
