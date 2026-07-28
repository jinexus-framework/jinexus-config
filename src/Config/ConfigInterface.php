<?php

declare(strict_types=1);

namespace JiNexus\Config\Config;

use JiNexus\Config\Base\BaseInterface;

/**
 * Interface ConfigInterface
 * @package JiNexus\Config\Config
 */
interface ConfigInterface extends BaseInterface
{
    /**
     * ConfigInterface constructor
     */
    public function __construct();

    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @param array $config
     */
    public function setConfig(array $config = []);

    /**
     * Get a value and return $default if there is no element set.
     *
     * @param $needle
     * @param null $default
     * @return mixed|null
     */
    public function get($needle, mixed $default = null): mixed;

    /**
     * Set a value in the config
     *
     * @param string $needle
     * @param $value
     * @return $this
     */
    public function set(string $needle, $value): ConfigInterface;

    /**
     * Magic method to get a config needle, forwards to $this->get()
     *
     * @param $needle
     * @return mixed|null
     */
    public function __get($needle);

    /**
     * Magic method to set a config $needle, forwards to $this->set()
     *
     * @param string $needle
     * @param $value
     * @return $this
     */
    public function __set(string $needle, $value);

    /**
     * Check if a needle exists in the config
     *
     * @param $needle
     * @return bool
     */
    public function has($needle): bool;
}
