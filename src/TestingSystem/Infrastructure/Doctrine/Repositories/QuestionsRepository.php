<?php

declare(strict_types=1);

namespace App\TestingSystem\Infrastructure\Doctrine\Repositories;

use App\TestingSystem\Application\Repositories\QuestionsRepositoryInterface;
use App\TestingSystem\Domain\Model\Question;
use Doctrine\Persistence\ManagerRegistry;

class QuestionsRepository extends AbstractRepository implements QuestionsRepositoryInterface
{
    public function __construct(
        ManagerRegistry $doctrine,
    ) {
        parent::__construct(Question::class, $doctrine);
    }

    /**
     * @param \App\TestingSystem\Domain\Model\Id[] $ids
     *
     * @return \App\TestingSystem\Domain\Model\Question[]
     */
    public function fetchByIds(array $ids): array
    {
        return $this->getRepository()->createQueryBuilder('q')
            ->where('q.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
    }
}
