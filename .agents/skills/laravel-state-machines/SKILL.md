---
name: laravel-state-machines
description: State machines using laravel-eloquent-state-machines (asantibanez) with built-in transition history/audit trail. Use when implementing or modifying state machines, transitions, or transition validation.
---

# Laravel State Machines

State machines with `asantibanez/laravel-eloquent-state-machines` — array-defined transitions per field, validation/side-effect hooks, optional built-in audit trail.

## Core Concept

**[state-management.md](references/state-management.md)** - State machine patterns:
- `HasStateMachines` trait + `$stateMachines` map
- `StateMachine` class: `transitions()`, `defaultState()`, `recordHistory()`
- Validation (`validatorForTransition`) and hooks (`beforeTransitionHooks`/`afterTransitionHooks`)
- Transition history / audit trail
- When to use state machines vs simple enums

## Pattern

```php
final class OrderStatusStateMachine extends StateMachine
{
    public function transitions(): array
    {
        return [
            'draft' => ['pending'],
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
        ];
    }

    public function defaultState(): ?string
    {
        return 'draft';
    }

    public function recordHistory(): bool
    {
        return true; // audit trail — who/when changed the order status
    }

    public function afterTransitionHooks(): array
    {
        return [
            'completed' => [
                fn ($from, $order) => OrderCompleted::dispatch($order),
            ],
        ];
    }
}

class Order extends Model
{
    use HasStateMachines;

    public $stateMachines = [
        'status' => OrderStatusStateMachine::class,
    ];
}

// Usage
$order->status()->transitionTo('pending', ['note' => 'Paid'], auth()->user());
$order->status()->canBe('processing');
$order->status()->is('pending');
$order->status()->history()->get(); // requires recordHistory() === true
```

Use state machines for transitions with validation, side effects, or when an audit trail of changes is needed. Use simple enums for basic status fields without transition rules.
