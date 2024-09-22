<?php

declare(strict_types=1);

namespace App\TestingSystem\Application\Repositories;

use App\TestingSystem\Domain\Model\Submission;

interface SubmissionsRepositoryInterface
{
    public function add(Submission $submission): void;
}
