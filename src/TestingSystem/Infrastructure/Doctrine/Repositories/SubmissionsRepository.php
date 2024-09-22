<?php

declare(strict_types=1);

namespace App\TestingSystem\Infrastructure\Doctrine\Repositories;

use App\TestingSystem\Application\Repositories\SubmissionsRepositoryInterface;
use App\TestingSystem\Domain\Model\Submission;
use Doctrine\Persistence\ManagerRegistry;

class SubmissionsRepository extends AbstractRepository implements SubmissionsRepositoryInterface
{
    public function __construct(
        ManagerRegistry $doctrine,
    ) {
        parent::__construct(Submission::class, $doctrine);
    }

    public function add(Submission $submission): void
    {
        $this->getEntityManager()->persist($submission);
    }
}
