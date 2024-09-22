<?php

declare(strict_types=1);

namespace App\TestingSystem\Application;

/**
 * Interface for flushing pending UnitOfWork changes.
 */
interface FlusherInterface
{
    /**
     * @param class-string $className Class name of managed entity (for multiple entity-manager setups).
     *                                If there are multiple entities with common entity manager - you need to pass a single class name.
     */
    public function flush(string $className): void;
}
