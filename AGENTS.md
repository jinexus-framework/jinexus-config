# AGENTS.md

Guidance for AI coding agents (and humans) working in the `jinexus-framework/jinexus-config` package. 
Read this before making changes.

## What this package is

A tiny, dependency-free configuration component for the JiNexus Framework. 
It stores configuration as an in-memory associative array and exposes it through explicit 
accessors (`get`/`set`/`has`), magic property access (`__get`/`__set`), and a reflection-based 
magic getter/setter (`__call`). It has **no I/O** — no file loading, no environment parsing, no network. 
Consumers are responsible for populating a `Config` instance from whatever source they choose.

## Build & test commands

Run everything from the package root (the directory containing `composer.json`).

```bash
# Install dependencies
composer install

# Regenerate autoloader after adding/moving/renaming classes or namespaces
composer dump-autoload

# Run the full test suite (auto-discovers phpunit.dist.xml)
./vendor/bin/phpunit

# Equivalent via Composer scripts (what CI runs)
composer test
composer test:coverage

# Same, but silence the local Xdebug "could not connect" notice
XDEBUG_MODE=off ./vendor/bin/phpunit

# Readable, per-test output
XDEBUG_MODE=off ./vendor/bin/phpunit --testdox

# Run a single file
XDEBUG_MODE=off ./vendor/bin/phpunit test/Config/ConfigTest.php

# Run a single test by name (regex against method names)
XDEBUG_MODE=off ./vendor/bin/phpunit --filter set_with_an_empty_needle_appends_a_numeric_key
```

There is no build step — this is a source-only library consumed via Composer.

## Project architecture

```
src/
  ConfigException.php            JiNexus\Config\ConfigException — base exception (extends \Exception)
  Base/
    BaseInterface.php            Declares __call()
    AbstractBase.php             Reflection-based magic getX()/setX() for PUBLIC properties;
                                 throws ConfigException on anything it can't resolve
  Config/
    ConfigInterface.php          The config contract (get/set/has/getConfig/setConfig/__get/__set)
    AbstractConfig.php           The real implementation: array store + get/set/has + __get/__set
    Config.php                   Concrete, intentionally EMPTY subclass of AbstractConfig
    Factory/
      ConfigFactory.php          static build(): Config
  Factory/
    FactoryInterface.php         Marker interface extending BaseInterface
    AbstractFactory.php          Base for factories (extends AbstractBase)

test/                            Namespace: JiNexus\Config\Test\
  Base/AbstractBaseTest.php      Covers AbstractBase::__call (public/protected/unknown paths)
  Config/ConfigTest.php          Covers Config/AbstractConfig behavior
  Config/Factory/ConfigFactoryTest.php
  Fixture/BaseDouble.php         Test double with a public + protected property for __call tests
```

Inheritance chain: `Config` → `AbstractConfig` → `AbstractBase`, and `ConfigFactory` → `AbstractFactory` → `AbstractBase`.

## Coding standards

- **Language:** PHP `^8.5`. Every PHP file starts with `declare(strict_types=1);`.
- **Autoloading:** PSR-4. `JiNexus\Config\` → `src/`, `JiNexus\Config\Test\` → `test/`. 
  One class/interface per file; the file name matches the type name.
- **Naming:** interfaces are suffixed `Interface` (`ConfigInterface`); abstract bases are 
  prefixed `Abstract` (`AbstractConfig`). Namespaces mirror the directory layout.
- **`Config` stays empty.** Do **not** declare real properties (e.g. `public mixed $timezone;`) 
  on `Config` or `AbstractConfig` for individual config keys. Declared properties shadow `__get`/`__set`, 
  so dynamic keys stop flowing into the `$config` array and typed-but-uninitialized property errors appear. 
  If you only need to silence an IDE "undefined property" notice, use a `@property` PHPDoc tag on the 
  class docblock — never a real property.
- **Errors:** throw `JiNexus\Config\ConfigException` (or a subclass of it) for all package-level 
  failures. `AbstractBase::__call` already throws it for unresolved magic calls.
- **PHP 8.5 features are in use.** The test suite uses newer syntax (e.g., the pipe operator `|>`). 
  Keep the `php: "^8.5"` constraint in mind — do not add code that requires a version higher than the 
  declared floor, and do not lower the floor to accommodate a tool that doesn't understand 8.5 syntax.

### Test conventions

- Tests extend `PHPUnit\Framework\TestCase` and are declared `final`.
- Use PHPUnit **attributes**, not annotations: `#[Test]`, `#[CoversClass(...)]`.
- Test method names are `snake_case` and describe the behavior (`get_returns_the_supplied_default_when_key_is_missing`).
- PHPUnit 13: use `expectExceptionMessageMatches()` (regex), **not** the deprecated `expectExceptionMessage()`. 
  Wrap literal text with `preg_quote($text, '/')`.
- When a test deliberately exercises magic access or calls an unsupported magic method, 
  suppress the specific IDE inspection on that line only 
  (`//noinspection PhpUndefinedFieldInspection` / `PhpUndefinedMethodInspection`) with a comment saying 
  why — don't disable inspections globally and don't add misleading `@method`/`@property` tags 
  for members that are supposed to fail.
- Prefer assertions that reflect a real runtime contract over ones a type checker can fold to a constant 
  (e.g., assert on `class_implements(...)` rather than `assertInstanceOf` against an already-typed value).

## Workflow rules

- **Before finishing any change, run the suite** and make sure it's green: `XDEBUG_MODE=off ./vendor/bin/phpunit`.
- **After touching classes/namespaces**, run `composer dump-autoload`.
- **New behavior requires a new test.** This is a pure-logic library, so unit tests are expected to cover every 
  branch you add or change.
- **Commits:** follow [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/). 
  Format: `type(scope): description`, with an optional body and footers.
  - Common types: `feat`, `fix`, `docs`, `test`, `refactor`, `chore`, `build`, `ci`.
  - Scope is optional and names the affected area (e.g. `feat(config): …`, `test(factory): …`).
  - Subject is imperative and lowercase, no trailing period.
  - Breaking changes: add `!` after the type/scope (`feat!:`) and a `BREAKING CHANGE:` footer describing the break 
    and its migration.
  - Example: `feat(config)!: remove array argument from constructor`.
- **Changelog:** update `CHANGELOG.md` for every user-visible change, following the Keep a Changelog structure already 
  in the file (Added / Deprecated / Removed / Fixed). Newest release on top.
- **Versioning:** semantic versioning. When bumping the minor/major line, also update `extra.branch-alias.dev-main` 
  in `composer.json` to match the next dev series.
- **Config files:** `phpunit.dist.xml` is the committed default; a local `phpunit.xml` (gitignored) overrides it for 
  personal tweaks. Don't commit `phpunit.xml`, `vendor/`, or `.phpunit.cache/`.
- **CI:** `.github/workflows/php.yml` runs on pushes and pull requests to `main` — it validates `composer.json`, 
  installs on PHP 8.5 (pinned via `shivammathur/setup-php`), and runs `composer test`. Keep the workflow's PHP version 
  in sync with the `require.php` constraint in `composer.json`.
```
