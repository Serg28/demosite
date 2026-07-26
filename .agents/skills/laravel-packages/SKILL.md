---
name: laravel-packages
description: Package development and extraction of reusable code. Use when creating, extracting, or modifying composer packages.
---

# Laravel Packages

Package development: extracting reusable patterns for use across projects.

**Related guides:**
- [package-extraction.md](references/package-extraction.md) - Extracting code into packages
- [DTOs](../laravel-dtos/SKILL.md) - Using Spatie Laravel Data
- [Models](../laravel-models/SKILL.md) - Model integration with optional state machines / query builder packages

## When to Extract

Extract to package when:
1. Pattern used in 3+ projects
2. Code is stable and well-tested
3. Pattern has clear boundaries
4. Maintenance cost justified

**[→ Complete extraction guide: package-extraction.md](references/package-extraction.md)**

## Package Structure

```
my-package/
├── src/
│   ├── PackageServiceProvider.php
│   ├── Actions/
│   ├── DTOs/
│   └── ...
├── tests/
├── composer.json
└── README.md
```

Use semantic versioning. Test packages independently. Document clearly.

## Core Packages (Always)

Foundational to the architecture/tooling itself, not tied to a specific feature.

### Spatie Laravel Data
```bash
composer require spatie/laravel-data
```
- DTOs with casting, validation, transformers
- Test factory support

### Pest
```bash
composer require pestphp/pest pestphp/pest-plugin-laravel --dev
```
- Expressive testing framework
- Architecture tests

## Optional Packages

Install only when the concrete need described in **When** actually exists in the project — not upfront.

### Laravel Eloquent State Machines
```bash
composer require asantibanez/laravel-eloquent-state-machines
```
**When:** Entity has complex state transitions needing validation, side effects, or an audit trail (see [laravel-state-machines](../laravel-state-machines/SKILL.md))

### Spatie Query Builder
```bash
composer require spatie/laravel-query-builder
```
**When:** simple Eloquent-backed models or API endpoints need client-driven filter/sort/include
via query strings. **Not for catalog/product search** — that already goes through Elasticsearch
(linecore-demo) / TypeSense (demo.loc) services, this package operates on Eloquent queries only.

### Laravel Sanctum
```bash
composer require laravel/sanctum
```
**When:** API authentication needed

### Stancl Tenancy
```bash
composer require stancl/tenancy
```
**When:** Multi-tenant application

## Not Used In This Project

### Saloon
Not needed — external integrations already follow a consistent, working convention:
`app/Services/{Domain}/Gateways/{Provider}Client.php` using `Illuminate\Support\Facades\Http`
(payment gateways: LiqPay, MonoPay, WayForPay, Privat, EasyPay, NovaPay, PayLink, Platon;
SMS drivers: TurboSms, Vodafone, AlphaName). Adding Saloon would introduce a second,
competing style for the same problem. New integrations follow the existing `*Client` + `Http::` pattern.
Reconsider only if the user explicitly asks to refactor integrations onto Saloon.

### Spatie Settings
Not needed — the Linecore CMS engine already has its own settings system
(`Setting` model, `config/cms/settings.php`, CMS `Definitions/Settings*.php`).

## Installation

```bash
composer require spatie/laravel-data
composer require pestphp/pest pestphp/pest-plugin-laravel --dev
./vendor/bin/pest --init
```

Add optional packages above only when their **When** condition applies to the current work.
