<?php

declare(strict_types=1);

namespace App\TestingSystem\Domain\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'submissions')]
class Submission
{
    #[ORM\Id]
    #[ORM\Column(type: 'common__id', unique: true)]
    private Id $id;

    #[ORM\Column(type: 'common__id')]
    private Id $testId;

    /** @var array<array{questionId: string, question: string, selectedChoices: string[], isCorrect: bool}> $data */
    #[ORM\Column(type: 'json')]
    private array $data;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    /**
     * @param array<array{questionId: string, question: string, selectedChoices: string[], isCorrect: bool}> $data
     */
    public function __construct(
        Id $id,
        Id $testId,
        array $data = [],
        ?DateTimeImmutable $createdAt = null,
    ) {
        $this->id        = $id;
        $this->testId    = $testId;
        $this->data      = $data;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getTestId(): Id
    {
        return $this->testId;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param string[] $selectedChoices
     */
    public function addAnswer(Id $questionId, string $question, array $selectedChoices, bool $isCorrect): void
    {
        foreach ($this->data as $answer) {
            if ($answer['questionId'] === $questionId->toString()) {
                throw new DomainException('Answer already exists.');
            }
        }

        $this->data[] = [
            'questionId'      => $questionId->toString(),
            'question'        => $question,
            'selectedChoices' => $selectedChoices,
            'isCorrect'       => $isCorrect,
        ];
    }
}
