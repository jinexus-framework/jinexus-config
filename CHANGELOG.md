# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## v1.1.0 - 2026-07-30

### Added

- `AbstractConfig` accessors: `get()` (with default), `set()` (fluent), `has()`, `getConfig()`, and `setConfig()`.
- Magic property access on config objects via `__get()`/`__set()`, forwarding to `get()`/`set()`.
- Reflection-based magic getters/setters for public properties via `AbstractBase::__call()`.
- `ConfigFactory::build()` for constructing a fresh `Config` instance.
- `ConfigException` for package-level error handling.
- `ConfigInterface`, `BaseInterface`, and `FactoryInterface` contracts.
- PHPUnit 13 unit-test suite covering `Config`, `AbstractBase`, and `ConfigFactory`, with a committed `phpunit.dist.xml`.
- `composer test` / `composer test:coverage` scripts.
- GitHub Actions CI workflow (`.github/workflows/php.yml`) that validates `composer.json`, installs dependencies on PHP 8.5, and runs the test suite.
- `AGENTS.md` with build, coding-standard, architecture, and workflow guidance.
- Expanded `README.md` with installation, usage, and testing documentation.

### Changed

- Raised the minimum PHP requirement to `^8.5`.

### Deprecated

- Nothing.

### Removed

- The constructor no longer accepts configuration data as an array argument. Populate a `Config` instance with `setConfig()` or `set()` instead.

### Fixed

- Nothing.

## v1.0.0 - 2018-07-10

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
