<?php

declare(strict_types=1);

namespace App\TestingSystem\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tests')]
class Test
{
    #[ORM\Id]
    #[ORM\Column(type: 'common__id', unique: true)]
    private Id $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    /** @var Collection<array-key,TestQuestion> $questions */
    #[ORM\OneToMany(targetEntity: TestQuestion::class, mappedBy: 'test', cascade: ['all'])]
    private Collection $questions;

    public function __construct(
        Id $id,
        string $name,
    ) {
        $this->id        = $id;
        $this->name      = $name;
        $this->questions = new ArrayCollection();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Id[]
     */
    public function getQuestionsIds(): array
    {
        return array_map(static fn (TestQuestion $question): Id => $question->getQuestionId(), $this->questions->toArray());
    }

    public function addQuestion(Id $questionId): void
    {
        $this->questions->add(new TestQuestion(Id::next(), $this, $questionId));
    }
}
