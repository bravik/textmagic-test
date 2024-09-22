<?php

declare(strict_types=1);

namespace App\Contracts\TestingSystem;

/**
 * The contract can be stricter modeled with objects, but i don't want to spend time on this
 */
interface TestingSystemInterface
{
    /**
     * @return array<array{id: string, name: string}>
     */
    public function fetchAvailableTests(): array;

    /**
     * * @return array{id: string, name: string, questions: array<array-key,array{ id: string, text: string, choices: string[], answer: int[]}>}
     */
    public function getTest(string $id): array;

    /**
     * @param array<string,array<int>> $answers - questionId => [choiceNumber1, choiceNumber2, ...]
     */
    public function submitTest(string $testId, array $answers): void;
}
