# JiNexus Config

`JiNexus Config` provides simple, in-memory access to configuration data within an
application. It stores configuration as an associative array and exposes it through
explicit accessors, magic property access, and reflection-based getters/setters.

It is intentionally small and has **no external dependencies** and **no I/O** — you
decide where the configuration data comes from (files, environment, a database, plain
arrays) and hand it to a `Config` instance.

- Documentation: https://framework.jinexus.com/documentation
- Issues: https://github.com/jinexus-framework/jinexus-config/issues

## Requirements

- PHP `^8.5`

## Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require jinexus-framework/jinexus-config
```

## Usage

### Creating a config

```php
use JiNexus\Config\Config\Config;
use JiNexus\Config\Config\Factory\ConfigFactory;

// Directly...
$config = new Config();

// ...or via the factory (returns a fresh Config each call).
$config = ConfigFactory::build();
```

### Reading and writing values

```php
// Set values (fluent — set() returns the Config instance).
$config->set('host', 'localhost')
       ->set('port', 8080);

// Get a value with an optional default when the key is absent.
$config->get('host');              // 'localhost'
$config->get('timeout');           // null
$config->get('timeout', 30);       // 30

// Check for presence (uses array_key_exists, so a key set to null still "has").
$config->has('host');              // true
$config->set('debug', null);
$config->has('debug');             // true
```

### Magic property access

`__get` and `__set` forward to `get()` and `set()`, so you can treat config keys like
properties:

```php
$config->timezone = 'UTC';   // same as $config->set('timezone', 'UTC')
$config->timezone;           // 'UTC' — same as $config->get('timezone')
$config->missing;            // null
```

### Bulk access

```php
$config->setConfig(['a' => 1, 'b' => 2]); // replace the entire store
$config->getConfig();                      // ['a' => 1, 'b' => 2]
$config->setConfig();                      // reset to []
```

### Error handling

Package-level failures throw `JiNexus\Config\ConfigException` (a subclass of
`\Exception`). For example, calling an unsupported magic getter/setter resolved through
`AbstractBase::__call` throws a `ConfigException` with a `Not implemented: …` message.

## Extending

`Config` is a deliberately empty subclass of `AbstractConfig`. Keep it that way — do not
declare real properties for individual config keys, because a declared property shadows
the `__get`/`__set` magic and stops that key from flowing into the internal store. If you
want IDE autocompletion for known keys, add `@property` PHPDoc tags to the class docblock
rather than real properties.

## Testing

```bash
composer install
./vendor/bin/phpunit
```

`phpunit.dist.xml` is the committed configuration. Use `XDEBUG_MODE=off` to silence the
local Xdebug notice, and `--testdox` for readable output:

```bash
XDEBUG_MODE=off ./vendor/bin/phpunit --testdox
```

## Contributing

Please see [CONDUCT.md](CONDUCT.md) for the code of conduct. Contributions should include
tests and a `CHANGELOG.md` entry. See [AGENTS.md](AGENTS.md) for detailed build, style,
and workflow conventions.

## License

BSD-3-Clause. See [LICENSE.md](LICENSE.md).
