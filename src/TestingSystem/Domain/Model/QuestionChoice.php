<?php

declare(strict_types=1);

namespace App\TestingSystem\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'questions_choices')]
class QuestionChoice
{
    #[ORM\Id]
    #[ORM\Column(type: 'common__id', unique: true)]
    private Id $id;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'choices')]
    private Question $question;

    #[ORM\Column(type: 'string')]
    private string $text;

    #[ORM\Column(type: 'boolean')]
    private bool $isCorrect;

    public function __construct(
        Id $id,
        Question $question,
        string $text,
        bool $isCorrect,
    ) {
        $this->id        = $id;
        $this->question  = $question;
        $this->text      = $text;
        $this->isCorrect = $isCorrect;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }
}
