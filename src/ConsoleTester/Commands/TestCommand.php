<?php

declare(strict_types=1);

namespace App\ConsoleTester\Commands;

use App\ConsoleTester\Model\TestSessionFactory;
use App\ConsoleTester\State;
use App\Contracts\TestingSystem\TestingSystemInterface;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(
    name: 'app:test',
    description: 'Runs a test...',
)]
class TestCommand extends Command
{
    public function __construct(
        private TestingSystemInterface $testingSystem,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hi, there!');

        /** @var QuestionHelper $questionsHelper */
        $questionsHelper = $this->getHelper('question');

        $state           = State::CHOOSING_TEST;
        $lastTestSession = null;

        // We'll fetch tests once per command run
        $availableTests = $this->testingSystem->fetchAvailableTests();

        // Exit with Ctrl + C
        while (true) {
            switch ($state) {
                /*
                 * Present a choice of available tests.
                 * When one picked, create a testing session.
                 * Questions and choices are shuffled in scope of session only, when it is created.
                 */
                case State::CHOOSING_TEST:
                    /** @var string $selectedTest */
                    $selectedTest = $questionsHelper->ask(
                        $input,
                        $output,
                        new ChoiceQuestion(
                            'Which test would you like to take?',
                            choices: array_map(static fn (array $test): string => $test['name'], $availableTests)
                        )
                    );

                    $selectedTestId = null;
                    foreach ($availableTests as $test) {
                        if ($test['name'] === $selectedTest) {
                            $selectedTestId = $test['id'];
                        }
                    }

                    if ($selectedTestId === null) {
                        $output->writeln('Invalid test selection');

                        continue 2;
                    }

                    // Create testing session
                    $lastTestSession = TestSessionFactory::createFromCoreResponse(
                        $this->testingSystem->getTest($selectedTestId),
                    );

                    $state = State::TESTING;
                    break;

                    /*
                     * Loop through test questions.
                     * When there no more unanswered questions, submit test and proceed to results
                     */
                case State::TESTING:
                    if ($lastTestSession === null) {
                        throw new RuntimeException('Invalid state');
                    }

                    $question = $lastTestSession->getNextUnansweredQuestion();

                    // If no more unanswered questions left, submit test and proceed to results
                    if ($question === null) {
                        $this->testingSystem->submitTest(
                            $lastTestSession->testId,
                            $lastTestSession->getAnswers()
                        );

                        $state = State::RESULTS;

                        continue 2;
                    }

                    $choices = $question->getChoices();

                    $consoleQuestion = new ChoiceQuestion(
                        $question->text,
                        choices: $choices,
                    );
                    $consoleQuestion->setMultiselect(true);

                    /** @var string[] $userInput */
                    $userInput = $questionsHelper->ask(
                        $input,
                        $output,
                        $consoleQuestion,
                    );

                    $answers = [];
                    foreach ($choices as $index => $choice) {
                        if (in_array($choice, $userInput, true)) {
                            $answers[] = (int) $index;
                        }
                    }

                    $question->answer($answers);
                    break;

                    /*
                     * This is a transactional state, just not to overwhelm the previous step.
                     * We do not stay here and await user input,
                     * but simply print the report and go back to the test selection.
                     */
                case State::RESULTS:
                    if ($lastTestSession === null || $lastTestSession->isComplete() === false) {
                        throw new RuntimeException('Invalid state');
                    }

                    $output->writeln('------------------------');
                    $output->writeln('RESULT');

                    $correctAnswersList = [];
                    $wrongAnswersList   = [];
                    foreach ($lastTestSession->getQuestions() as $index => $question) {
                        if ($question->isAnsweredCorrectly()) {
                            $correctAnswersList[] = $index + 1 . ') ' . $question->text;
                        } else {
                            $wrongAnswersList[] = $index + 1 . ') ' . $question->text;
                        }
                    }

                    if (!empty($correctAnswersList)) {
                        $output->writeln('Correct answers:');
                        foreach ($correctAnswersList as $line) {
                            $output->writeln($line);
                        }
                    }

                    if (!empty($wrongAnswersList)) {
                        $output->writeln('Wrong answers:');
                        foreach ($wrongAnswersList as $line) {
                            $output->writeln($line);
                        }
                    }

                    $state = State::CHOOSING_TEST;
                    break;
                default:
                    throw new RuntimeException('Impossible state');
            }
        }

        return Command::SUCCESS;
    }
}
