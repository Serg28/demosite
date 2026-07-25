---
paths:
  - "**/*.php"
---

# Іменування (доповнення до Laravel-конвенцій)

- Булеві методи — префікс `is/has/can/should` (`isActive()`, не `activeFlag()`).
- Числові змінні — одиниця виміру в імені (`$timeoutInSeconds`, не `$timeout`).
- Symmetric pairs для протилежних операцій: `begin/end`, `lock/unlock`, `min/max`.

| Сутність | Конвенція | ✅ | ❌ |
|---|---|---|---|
| Route | множина | `articles/1` | `article/1` |
| Route name | snake_case | `users.show_active` | `users.show-active` |
| Relation hasOne/belongsTo | однина | `articleComment` | `articleComments` |
| Relation hasMany/belongsToMany | множина | `articleComments` | `articleComment` |
| Pivot table | однина, алфавітний порядок моделей | `article_user` | `user_article` |
| Foreign key | однина моделі + `_id` | `article_id` | `id_article` |
| View | kebab-case | `show-filtered.blade.php` | `showFiltered.blade.php` |
| Config property | snake_case | `users.max_articles` | `users.max-articles` |
| Enum | однина | `UserType` | `UserTypes` |
| Form Request | дія + Request | `UpdateUserRequest` | `UserRequest` |
| Action | однина, без суфікса Action | `PlaceOrder` | `PlaceOrderAction` |
| Exception | однина | `ModelNotFoundException` | `ModelNotFound` |
| Trait | прикметник, без суфікса Trait | `Notifiable` | `NotificationTrait` |
