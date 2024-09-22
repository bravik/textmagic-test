<?php

declare(strict_types=1);

namespace App\ConsoleTester\Model;

class TestSession
{
    /**
     * @param array<int,Question> $questions
     */
    public function __construct(
        public readonly string $testId,
        private array $questions,
    ) {
    }

    public function isComplete(): bool
    {
        foreach ($this->questions as $question) {
            if (!$question->isAnswered()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int,Question>
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function getNextUnansweredQuestion(): ?Question
    {
        foreach ($this->questions as $question) {
            if (!$question->isAnswered()) {
                return $question;
            }
        }

        return null;
    }

    /**
     * @return array<string, int[]>
     */
    public function getAnswers(): array
    {
        $answers = [];
        foreach ($this->questions as $question) {
            $answers[$question->id] = $question->getSubmittedAnswer();
        }

        return $answers;
    }
}
