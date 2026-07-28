<?php

declare(strict_types=1);

namespace JiNexus\Config\Test\Config\Factory;

use JiNexus\Config\Config\ConfigInterface;
use JiNexus\Config\Config\Factory\ConfigFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigFactory::class)]
final class ConfigFactoryTest extends TestCase
{
    #[Test]
    public function it_builds_something_usable_as_a_config(): void
    {
        // build()'s return type is Config (PHP enforces it at the return), so
        // asserting instanceof on the result folds to always-true. Instead, we
        // check the produced class advertises the ConfigInterface contract.
        ConfigFactory::build()
            |> class_implements(...)
            |> (fn($x) => self::assertContains(ConfigInterface::class, $x));
    }

    #[Test]
    public function build_returns_a_fresh_instance_each_call(): void
    {
        self::assertNotSame(ConfigFactory::build(), ConfigFactory::build());
    }

    #[Test]
    public function built_instances_are_independent(): void
    {
        $one = ConfigFactory::build();
        $two = ConfigFactory::build();

        $one->set('shared', 'only-on-one');

        self::assertFalse($two->has('shared'));
    }
}
