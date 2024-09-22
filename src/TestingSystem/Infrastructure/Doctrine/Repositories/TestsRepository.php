<?php

declare(strict_types=1);

namespace App\TestingSystem\Infrastructure\Doctrine\Repositories;

use App\TestingSystem\Application\Repositories\TestsRepositoryInterface;
use App\TestingSystem\Domain\Model\Id;
use App\TestingSystem\Domain\Model\Test;
use Doctrine\Persistence\ManagerRegistry;

class TestsRepository extends AbstractRepository implements TestsRepositoryInterface
{
    public function __construct(
        ManagerRegistry $doctrine,
    ) {
        parent::__construct(Test::class, $doctrine);
    }

    /**
     * @throws NotFoundException
     */
    public function get(Id $id): Test
    {
        $entity = $this->getRepository()->find($id);

        if ($entity === null) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @return Test[]
     */
    public function fetchAll(): array
    {
        return $this->getRepository()->findAll();
    }
}
