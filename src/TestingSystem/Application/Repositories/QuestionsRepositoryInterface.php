<?php

declare(strict_types=1);

namespace App\TestingSystem\Application\Repositories;

use App\TestingSystem\Domain\Model\Id;
use App\TestingSystem\Domain\Model\Question;

interface QuestionsRepositoryInterface
{
    /**
     * @param Id[] $ids
     *
     * @return Question[]
     */
    public function fetchByIds(array $ids): array;
}
