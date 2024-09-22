<?php

declare(strict_types=1);

namespace App\TestingSystem\Application;

use App\Contracts\TestingSystem\TestingSystemInterface;
use App\TestingSystem\Application\Repositories\QuestionsRepositoryInterface;
use App\TestingSystem\Application\Repositories\SubmissionsRepositoryInterface;
use App\TestingSystem\Application\Repositories\TestsRepositoryInterface;
use App\TestingSystem\Domain\Model\Id;
use App\TestingSystem\Domain\Model\Submission;
use App\TestingSystem\Domain\Model\Test;

/**
 * TODO Service injectino can be optimized with symfony service subscribers, or just split into smaller query/use-case classes
 * TODO Types can be enforced with objects instead of arrays
 */
class TestingSystem implements TestingSystemInterface
{
    public function __construct(
        private TestsRepositoryInterface $testsRepository,
        private QuestionsRepositoryInterface $questionsRepository,
        private SubmissionsRepositoryInterface $submissionsRepository,
        private FlusherInterface $flusher,
    ) {
    }

    /**
     * @return array<array{id: string, name: string}>
     */
    public function fetchAvailableTests(): array
    {
        return array_map(
            static fn (Test $test): array => [
                'id'   => $test->getId()->toString(),
                'name' => $test->getName(),
            ],
            $this->testsRepository->fetchAll()
        );
    }

    /**
     * @return array{id: string, name: string, questions: array<array-key,array{ id: string, text: string, choices: string[], answer: int[]}>}
     */
    public function getTest(string $id): array
    {
        $test      = $this->testsRepository->get(Id::fromString($id));
        $questions = $this->questionsRepository->fetchByIds($test->getQuestionsIds());

        return [
            'id'        => $test->getId()->toString(),
            'name'      => $test->getName(),
            'questions' => array_map(
                static fn ($question) => [
                    'id'      => $question->getId()->toString(),
                    'text'    => $question->getText(),
                    'choices' => $question->getChoices(),
                    'answer'  => $question->getAnswers(),
                ],
                $questions
            ),
        ];
    }

    /**
     * @param array<string,array<int>> $answers - questionId => [choiceNumber1, choiceNumber2, ...]
     */
    public function submitTest(string $testId, array $answers): void
    {
        $test      = $this->testsRepository->get(Id::fromString($testId));
        $questions = $this->questionsRepository->fetchByIds($test->getQuestionsIds());

        $submission   = new Submission(Id::next(), $test->getId());
        $questionsMap = [];
        foreach ($questions as $question) {
            $questionsMap[$question->getId()->toString()] = $question;
        }

        foreach ($answers as $questionId => $answer) {
            $question = $questionsMap[$questionId];

            $choices      = $question->getChoices();
            $choicesTexts = [];
            foreach ($answer as $choice) {
                $choicesTexts[] = $choices[$choice];
            }

            $submission->addAnswer(
                questionId: $question->getId(),
                question: $question->getText(),
                selectedChoices: $choicesTexts,
                isCorrect: $question->checkAnswer($answer)
            );
        }

        $this->submissionsRepository->add($submission);

        $this->flusher->flush(Submission::class);
    }
}
