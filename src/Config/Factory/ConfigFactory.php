<?php

declare(strict_types=1);

namespace JiNexus\Config\Config\Factory;

use JiNexus\Config\Config\Config;
use JiNexus\Config\Factory\AbstractFactory;

/**
 * Class ConfigFactory
 * @package JiNexus\Config\Config\Factory
 */
class ConfigFactory extends AbstractFactory
{
    /**
     * @return Config
     */
    public static function build(): Config
    {
        return new Config();
    }
}
