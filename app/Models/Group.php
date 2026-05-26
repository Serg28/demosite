<?php

namespace App\Models;

use Cartalyst\Sentinel\Roles\EloquentRole;

/**
 * Sentinel роль / група користувача.
 * Використовується для оптових цін ($user->inRole('wholesale')),
 * прав доступу і сумісності з CMS Linecore Builder.
 *
 * @property string $slug  Ідентифікатор ролі (наприклад: 'retail', 'wholesale')
 * @property string $name  Назва ролі
 */
class Group extends EloquentRole
{
    protected $table = 'roles';
}
