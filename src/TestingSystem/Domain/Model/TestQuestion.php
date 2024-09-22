<?php

declare(strict_types=1);

namespace App\TestingSystem\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tests_questions')]
class TestQuestion
{
    #[ORM\Id]
    #[ORM\Column(type: 'common__id')]
    private Id $id;

    #[ORM\ManyToOne(targetEntity: Test::class, inversedBy: 'questions')]
    private Test $test;

    #[ORM\Column(type: 'common__id')]
    private Id $questionId;

    public function __construct(
        Id $id,
        Test $test,
        Id $questionId,
    ) {
        $this->id         = $id;
        $this->test       = $test;
        $this->questionId = $questionId;
    }

    public function getQuestionId(): Id
    {
        return $this->questionId;
    }
}
