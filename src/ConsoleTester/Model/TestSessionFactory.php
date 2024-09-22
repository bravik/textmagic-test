<?php

declare(strict_types=1);

namespace App\ConsoleTester\Model;

use Webmozart\Assert\Assert;

class TestSessionFactory
{
    public static function createFromCoreResponse(
        array $coreResponse,
        bool $shuffleQuestions = true,
        bool $shuffleChoices = true,
    ): TestSession {
        Assert::keyExists($coreResponse, 'id');
        Assert::stringNotEmpty($coreResponse['id']);
        Assert::keyExists($coreResponse, 'questions');
        Assert::isArray($coreResponse['questions']);
        $questions = [];

        if ($shuffleQuestions) {
            shuffle($coreResponse['questions']);
        }

        foreach ($coreResponse['questions'] as $question) {
            Assert::isArray($question);
            Assert::keyExists($question, 'id');
            Assert::stringNotEmpty($question['id']);
            Assert::keyExists($question, 'text');
            Assert::stringNotEmpty($question['text']);
            Assert::keyExists($question, 'choices');
            Assert::keyExists($question, 'answer');
            Assert::isArray($question['choices']);
            Assert::isArray($question['answer']);
            Assert::allInteger($question['answer']);
            Assert::allString($question['choices']);

            $questions[] = Question::create(
                $question['id'],
                $question['text'],
                $question['choices'],
                $question['answer'],
                $shuffleChoices,
            );
        }

        return new TestSession($coreResponse['id'], $questions);
    }
}
