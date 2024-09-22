<?php

declare(strict_types=1);

namespace App\ConsoleTester\Model;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * Choices can be shuffled.
 * Shuffled order is stored separately from original choices and is used for presentation only.
 */
class Question
{
    /**
     * @param string[] $choices
     * @param int[]    $choicesOrder    Indexes of $choices in shuffled order
     * @param int[]    $correctAnswer   Indexes of correct choices
     * @param int[]    $submittedAnswer Indexes of submitted choices
     */
    public function __construct(
        public readonly string $id,
        public readonly string $text,
        private array $choices,
        private array $choicesOrder,
        private array $correctAnswer,
        private array $submittedAnswer = [],
        private bool $isAnsweredCorrectly = false,
    ) {
        Assert::eq(count($choices), count($choicesOrder));
    }

    /**
     * @param string[] $choices
     * @param int[]    $correctAnswer
     */
    public static function create(
        string $id,
        string $text,
        array $choices,
        array $correctAnswer,
        bool $shuffleChoices,
    ): self {
        $choicesOrder = range(0, count($choices) - 1);

        if ($shuffleChoices) {
            shuffle($choicesOrder);
        }

        return new self(
            $id,
            $text,
            $choices,
            $choicesOrder,
            $correctAnswer,
        );
    }

    /**
     * @return string[]
     */
    public function getChoices(): array
    {
        $orderedAnswers = [];
        foreach ($this->choicesOrder as $index) {
            $orderedAnswers[] = $this->choices[$index];
        }

        return $orderedAnswers;
    }

    /**
     * @return int[]
     */
    public function getSubmittedAnswer(): array
    {
        return $this->submittedAnswer;
    }

    /**
     * @param int[] $choices
     *
     * @throws InvalidArgumentException
     */
    public function answer(array $choices): void
    {
        if (empty($choices)) {
            throw new InvalidArgumentException('Choices cannot be empty');
        }

        $reorderedChoices = [];
        foreach ($choices as $choice) {
            if (!isset($this->choicesOrder[$choice])) {
                throw new InvalidArgumentException('Invalid choice');
            }

            $reorderedChoices[] = $this->choicesOrder[$choice];
        }

        $this->submittedAnswer     = $reorderedChoices;
        $this->isAnsweredCorrectly = array_diff($this->submittedAnswer, $this->correctAnswer) === [];
    }

    public function isAnswered(): bool
    {
        return !empty($this->submittedAnswer);
    }

    public function isAnsweredCorrectly(): bool
    {
        return $this->isAnsweredCorrectly;
    }
}
