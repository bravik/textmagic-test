<?php

declare(strict_types=1);

namespace App\TestingSystem\Application\Repositories;

use App\TestingSystem\Domain\Model\Id;
use App\TestingSystem\Domain\Model\Test;

interface TestsRepositoryInterface
{
    public function get(Id $id): Test;

    /**
     * @return Test[]
     */
    public function fetchAll(): array;
}
