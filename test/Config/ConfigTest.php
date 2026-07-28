<?php

declare(strict_types=1);

namespace JiNexus\Config\Test\Config;

use JiNexus\Config\Config\Config;
use JiNexus\Config\Config\ConfigInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Config::class)]
final class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config();
    }

    #[Test]
    public function it_declares_the_config_interface(): void
    {
        // Check the class's interface list rather than the (already typed)
        // instance, so the assertion reflects a real contract instead of
        // folding to a compile-time constant.
        self::assertContains(ConfigInterface::class, class_implements(Config::class));
    }

    #[Test]
    public function it_starts_with_an_empty_config(): void
    {
        self::assertSame([], $this->config->getConfig());
    }

    #[Test]
    public function set_config_replaces_the_whole_array(): void
    {
        $this->config->setConfig(['a' => 1, 'b' => 2]);

        self::assertSame(['a' => 1, 'b' => 2], $this->config->getConfig());
    }

    #[Test]
    public function set_config_with_no_argument_resets_to_empty(): void
    {
        $this->config->setConfig(['a' => 1]);
        $this->config->setConfig();

        self::assertSame([], $this->config->getConfig());
    }

    #[Test]
    public function get_returns_the_stored_value(): void
    {
        $this->config->set('host', 'localhost');

        self::assertSame('localhost', $this->config->get('host'));
    }

    #[Test]
    public function get_returns_null_by_default_when_key_is_missing(): void
    {
        self::assertNull($this->config->get('missing'));
    }

    #[Test]
    public function get_returns_the_supplied_default_when_key_is_missing(): void
    {
        self::assertSame('fallback', $this->config->get('missing', 'fallback'));
    }

    #[Test]
    public function set_is_fluent(): void
    {
        self::assertSame($this->config, $this->config->set('key', 'value'));
    }

    #[Test]
    public function set_overwrites_an_existing_value(): void
    {
        $this->config->set('env', 'dev');
        $this->config->set('env', 'prod');

        self::assertSame('prod', $this->config->get('env'));
    }

    #[Test]
    public function set_with_an_empty_needle_appends_a_numeric_key(): void
    {
        // The falsy-needle branch: $config[] = $value.
        $this->config->set('', 'first');
        $this->config->set('', 'second');

        self::assertSame(['first', 'second'], $this->config->getConfig());
        self::assertSame('first', $this->config->get(0));
        self::assertSame('second', $this->config->get(1));
    }

    #[Test]
    public function has_is_true_for_a_set_key_and_false_otherwise(): void
    {
        $this->config->set('present', 'yes');

        self::assertTrue($this->config->has('present'));
        self::assertFalse($this->config->has('absent'));
    }

    #[Test]
    public function has_is_true_for_a_key_explicitly_set_to_null(): void
    {
        // Contract check: has() uses array_key_exists(), not isset(),
        // so a null value still counts as present.
        $this->config->set('nullable', null);

        self::assertTrue($this->config->has('nullable'));
        self::assertNull($this->config->get('nullable', 'default'));
    }

    #[Test]
    public function magic_set_forwards_to_set(): void
    {
        // Magic property write is the behavior under test: it must route
        // through AbstractConfig::__set() into the internal $config array.
        //noinspection PhpUndefinedFieldInspection
        $this->config->debug = true;

        self::assertTrue($this->config->get('debug'));
        self::assertTrue($this->config->has('debug'));
    }

    #[Test]
    public function magic_get_forwards_to_get(): void
    {
        $this->config->set('timezone', 'UTC');

        // Magic property read is the behavior under test: it must route
        // through AbstractConfig::__get() back to get().
        //noinspection PhpUndefinedFieldInspection
        self::assertSame('UTC', $this->config->timezone);
    }

    #[Test]
    public function magic_get_returns_null_for_a_missing_needle(): void
    {
        // Reading an unset needle via __get() must yield null, not an error.
        //noinspection PhpUndefinedFieldInspection
        self::assertNull($this->config->nope);
    }
}
