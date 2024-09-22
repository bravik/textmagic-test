<?php

declare(strict_types=1);

namespace App\TestingSystem\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Table(name: 'questions')]
class Question
{
    #[ORM\Id]
    #[ORM\Column(type: 'common__id', unique: true)]
    private Id $id;

    #[ORM\Column(type: 'string')]
    private string $text;

    /** @var Collection<int,QuestionChoice> $choices */
    #[ORM\OneToMany(targetEntity: QuestionChoice::class, mappedBy: 'question', cascade: ['all'])]
    private Collection $choices;

    public function __construct(
        Id $id,
        string $text,
    ) {
        $this->id      = $id;
        $this->text    = $text;
        $this->choices = new ArrayCollection();
    }

    public function addChoice(string $text, bool $isCorrect): self
    {
        $this->choices->add(new QuestionChoice(Id::next(), $this, $text, $isCorrect));

        return $this;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string[]
     */
    public function getChoices(): array
    {
        return array_map(static fn (QuestionChoice $choice): string => $choice->getText(), $this->choices->toArray());
    }

    /**
     * @return int[]
     */
    public function getAnswers(): array
    {
        $answers = [];
        foreach ($this->choices as $index => $choice) {
            if ($choice->isCorrect()) {
                $answers[] = $index;
            }
        }

        return $answers;
    }

    /**
     * @param int[] $answerNumbers
     */
    public function checkAnswer(array $answerNumbers): bool
    {
        Assert::notEmpty($answerNumbers, 'You must provide at least one answer');

        foreach ($answerNumbers as $answerNumber) {
            if (!isset($this->choices[$answerNumber]) || !$this->choices[$answerNumber]->isCorrect()) {
                return false;
            }
        }

        return true;
    }
}
