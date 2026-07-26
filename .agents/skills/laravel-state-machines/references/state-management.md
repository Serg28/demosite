# State Management

Use **asantibanez/laravel-eloquent-state-machines** for state management with array-defined transitions, validation/hooks, and an optional built-in audit trail — no separate history package needed.

**Related guides:**
- [Models](../../laravel-models/SKILL.md) - Model integration
- [Enums](../../laravel-enums/SKILL.md) - Simple state without transitions use enums
- [Packages](../../laravel-packages/SKILL.md) - Installing laravel-eloquent-state-machines

## When to Use State Machines

**Use state machines when:**
- Complex state transitions with rules
- Transition history/audit trail required
- Guards/validations per transition
- Side effects during transitions

**Use simple enums when:**
- Simple status fields
- No transition logic
- No side effects

See [Enums](../../laravel-enums/SKILL.md) for simple state management.

## StateMachine Class

One class per stateful field. Transitions are a plain array — no per-state classes:

```php
<?php

declare(strict_types=1);

namespace App\StateMachines;

use Asantibanez\LaravelEloquentStateMachines\StateMachines\StateMachine;

final class OrderStatusStateMachine extends StateMachine
{
    public function transitions(): array
    {
        return [
            'draft' => ['pending'],
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];
    }

    public function defaultState(): ?string
    {
        return 'draft';
    }

    public function recordHistory(): bool
    {
        return true; // off by default — turn on per state machine that needs an audit trail
    }
}
```

## Model Integration

```php
use Asantibanez\LaravelEloquentStateMachines\Traits\HasStateMachines;

class Order extends Model
{
    use HasStateMachines;

    public $stateMachines = [
        'status' => OrderStatusStateMachine::class,
    ];
}
```

The field itself (`status` column) stores the current state as a plain string — no custom cast needed.

## Validation

Return a `Validator`; if it `fails()`, the transition throws `ValidationException`:

```php
final class OrderStatusStateMachine extends StateMachine
{
    public function validatorForTransition($from, $to, $model): ?Validator
    {
        if ($to === 'processing') {
            return validator($model->toArray(), [
                'payment_confirmed_at' => 'required',
            ]);
        }

        return null;
    }
}
```

## Hooks (Side Effects)

`beforeTransitionHooks()` is keyed by the **`$from`** state, `afterTransitionHooks()` is keyed by the **`$to`** state. Each callback receives `($state, $model)`:

```php
final class OrderStatusStateMachine extends StateMachine
{
    public function afterTransitionHooks(): array
    {
        return [
            'completed' => [
                fn ($from, Order $order) => OrderCompleted::dispatch($order),
            ],
            'cancelled' => [
                fn ($from, Order $order) => $order->restock(),
            ],
        ];
    }
}
```

## Transitioning & Checking State

```php
// Transition — throws if not allowed or validation fails
$order->status()->transitionTo('processing', ['note' => 'Payment confirmed']);

// Explicit responsible model (defaults to auth()->user())
$order->status()->transitionTo('cancelled', ['reason' => 'Customer request'], $admin);

// Checks
$order->status()->canBe('completed');   // allowed by transitions() map?
$order->status()->is('pending');        // current state?
$order->status()->was('processing');    // ever was in this state (requires history)?
```

## Model Helper Methods

Wrap transitions/checks in intention-revealing model methods — same convention regardless of package:

```php
class Order extends Model
{
    use HasStateMachines;

    public function markAsCompleted(): self
    {
        $this->status()->transitionTo('completed');

        return $this;
    }

    public function canBeCancelled(): bool
    {
        return $this->status()->canBe('cancelled');
    }
}
```

## Transition History (Audit Trail)

Requires `recordHistory(): true` in the state machine. Stored in the package's `state_histories` table:

```php
$order->status()->history()->get();

$order->status()
    ->history()
    ->from('pending')
    ->to('processing')
    ->withCustomProperty('note', 'like', '%payment%')
    ->get();

$order->status()->responsible(); // who triggered the current state
```

## Query Builder

```php
Order::whereHasStatus(function ($query) {
    $query->withTransition('pending', 'processing');
})->get();
```

## Key Principles

1. Define allowed transitions as a plain array in `transitions()` — no per-state classes
2. Turn on `recordHistory()` for any status that needs an audit trail (e.g. orders)
3. Put side effects in `afterTransitionHooks()`, not in callers
4. Put transition guards in `validatorForTransition()`, not in controllers
5. Wrap `transitionTo()`/`canBe()` in intention-revealing model methods (`markAsX()`, `canBeX()`)

## Directory Structure

```
app/StateMachines/
└── OrderStatusStateMachine.php
```

No per-state class files — everything for one field lives in one `StateMachine` class.

## Summary

**State machines provide:**
- Array-defined transition rules
- Transition validation (`validatorForTransition`)
- Side effects via hooks (`beforeTransitionHooks`/`afterTransitionHooks`)
- Optional built-in audit trail (`recordHistory()` + `history()`)

**Use for complex states.** For simple statuses, use [Enums](../../laravel-enums/SKILL.md).
