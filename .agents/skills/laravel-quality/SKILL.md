---
name: laravel-quality
description: Code quality tooling with PHPStan, Pint, and strict types. Use when configuring or running static analysis, code style, or linting.
---

# Laravel Quality

Testing, static analysis, and code quality enforcement.

**Related guides:**
- [code-style.md](references/code-style.md) - Laravel Pint configuration and coding style
- [type-safety.md](references/type-safety.md) - Strict types and type hints
- [Testing](../laravel-testing/SKILL.md) - Comprehensive testing guide

## Quality Stack

```bash
# composer.json scripts
{
    "test": "pest",
    "analyse": "phpstan analyse",
    "format": "pint",
    "quality": [
        "@analyse",
        "@test"
    ]
}
```

All files must have `declare(strict_types=1)` at top. Run quality checks before every commit.

## Architecture Tests (Pest)

Enforce conventions with Pest architecture tests when appropriate for the project.

**[→ Architecture test examples: architecture-tests.md](references/architecture-tests.md)**

## Static Analysis (PHPStan)

### Installation
```bash
composer require phpstan/phpstan --dev
composer require phpstan/phpstan-strict-rules --dev
composer require larastan/larastan --dev
```

### Configuration

`phpstan.neon`
```neon
includes:
    - vendor/larastan/larastan/extension.neon
    - vendor/phpstan/phpstan-strict-rules/rules.neon

parameters:
    level: 5  # Minimum 5, increase per-project as practical (up to 8)
    paths:
        - app
        - tests
    excludePaths:
        - app/Providers/TelescopeServiceProvider.php
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: true
    reportUnmatchedIgnoredErrors: false
```

### Run
```bash
./vendor/bin/phpstan analyse
```

## Code Style (Laravel Pint)

### Installation
```bash
composer require laravel/pint --dev
```

### Configuration

`pint.json`
```json
{
    "preset": "laravel",
    "rules": {
        "simplified_null_return": true,
        "no_unused_imports": true,
        "ordered_imports": {
            "sort_algorithm": "alpha"
        }
    }
}
```

### Run
```bash
./vendor/bin/pint
./vendor/bin/pint --test  # Check only
```

## Test Coverage

### Enable Coverage (Pest)

`phpunit.xml`
```xml
<coverage>
    <report>
        <html outputDirectory="coverage"/>
        <text outputFile="php://stdout"/>
    </report>
</coverage>
```

### Run with Coverage
```bash
./vendor/bin/pest --coverage
./vendor/bin/pest --coverage --min=80  # Enforce minimum
```

## CI/CD Checks

### GitHub Actions Example

`.github/workflows/tests.yml`
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.4
          extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite
          coverage: xdebug

      - name: Install Dependencies
        run: composer install --prefer-dist --no-interaction

      - name: Code Style
        run: ./vendor/bin/pint --test

      - name: Static Analysis
        run: ./vendor/bin/phpstan analyse

      - name: Run Tests
        run: ./vendor/bin/pest --coverage --min=80
```

## Pre-commit Hooks — Not Used In This Project

`brainmaestro/composer-git-hooks` is a **git commit hook runner** (pre-commit/pre-push), not
package-level hooks — it wires `./vendor/bin/pint`/`phpstan`/`pest` to run automatically via
`composer.json`'s `extra.hooks`, replacing `.git/hooks/*` with a versioned config.

Not needed here:
- **linecore-demo** already has a native slot for this — `composer.json` → `post-install-cmd: "git config core.hooksPath .githooks"`, and a `pre-commit` script exists (runs `composer run style` i.e. Pint, then `git add .`). **Currently non-functional**: the hook file lives at `.git/.githooks/pre-commit`, but git resolves `core.hooksPath` relative to the repo root, so it looks for `.githooks/pre-commit` there — which doesn't exist (verified: `git rev-parse --git-path hooks/pre-commit` → `.githooks/pre-commit`, missing). Fix is to move the file to `<repo-root>/.githooks/pre-commit` (and commit it, it's currently untracked) — not to add a second package on top of a broken native one.
- **demo.loc** has no existing commit-time gate, and none has been requested; `.claude/hooks/check-rules.sh` already checks style/rules at edit-time in Claude Code sessions (a different layer — not a git hook).

Reconsider only if the user explicitly asks for automated commit-time quality gates.

## Quality Metrics

### What to Track

- **Test coverage** - Aim for 80%+
- **PHPStan level** - Level 8 (max)
- **Architecture test pass rate** - 100%
- **Code style violations** - 0

### Regular Reviews

- **Weekly** - Check test coverage trends
- **Per PR** - Run all quality checks
- **Monthly** - Review architecture compliance
- **Release** - Full quality audit

## Common Issues to Watch

### Anti-patterns
- Domain logic in controllers
- Using scopes instead of builders
- Missing strict types declaration
- Passing primitives instead of DTOs
- Jobs/Listeners with domain logic

### Type Safety
- Missing return types
- Missing parameter types
- Missing property types
- Untyped arrays/collections

### Testing
- Missing feature tests for endpoints
- Missing unit tests for actions
- Low coverage on critical paths
- Brittle tests (too many mocks)

## Enforcement Strategy

1. **Architecture tests** - Automated checks
2. **PR reviews** - Manual verification
3. **CI/CD gates** - Block failing builds
4. **Team standards** - Document + training
5. **Pair programming** - Share knowledge
