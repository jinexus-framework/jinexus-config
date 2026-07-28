<?php

declare(strict_types=1);

namespace JiNexus\Config\Base;

/**
 * Interface BaseInterface
 * @package JiNexus\Config\Base
 */
interface BaseInterface
{
    /**
     * Getters and Setters
     *
     * @param $property
     * @param array $arguments
     * @return mixed
     */
    public function __call($property, array $arguments);
}
