<?php

declare(strict_types=1);

namespace App\ConsoleTester\Commands;

use App\TestingSystem\Domain\Model\Id;
use App\TestingSystem\Domain\Model\Test;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * It shouldn't use any core objects, but that's for quick demonstration purposes only...
 *
 * @psalm-suppress all
 */
#[AsCommand(
    name: 'app:apply-fixtures',
    description: 'Applies fixtures',
)]
class ApplyFixturesCommand extends Command
{
    public function __construct(
        private ManagerRegistry $doctrine,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var EntityManagerInterface $em */
        $em = $this->doctrine->getManagerForClass(Test::class);

        $test = new Test(Id::next(), 'Sample Test');

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), 'What is the capital of France?'))
            ->addChoice('Paris', true)
            ->addChoice('London', false)
            ->addChoice('Berlin', false)
            ->addChoice('Moscow', true)
        ;
        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), 'What is the capital of Germany?'))
            ->addChoice('Paris', false)
            ->addChoice('London', false)
            ->addChoice('Berlin', true)
            ->addChoice('Moscow', false)
        ;

        foreach ($questions as $question) {
            $em->persist($question);
            $test->addQuestion($question->getId());
        }

        $em->persist($test);

        $em->flush();

        // Second test
        $questions = [];

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '1 + 1 ='))
            ->addChoice('3', false)
            ->addChoice('2', true)
            ->addChoice('0', false);

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '2 + 2 ='))
            ->addChoice('4', true)
            ->addChoice('3 + 1', true)
            ->addChoice('10', false);

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '3 + 3 ='))
            ->addChoice('1 + 5', true)
            ->addChoice('1', false)
            ->addChoice('6', true)
            ->addChoice('2 + 4', true);

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '4 + 4 ='))
            ->addChoice('8', true)
            ->addChoice('4', false)
            ->addChoice('0', false)
            ->addChoice('0 + 8', true);

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '5 + 5 ='))
            ->addChoice('6', false)
            ->addChoice('18', false)
            ->addChoice('10', true)
            ->addChoice('9', false)
            ->addChoice('0', false);

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '6 + 6 ='))
            ->addChoice('3', false)
            ->addChoice('9', false)
            ->addChoice('0', false)
            ->addChoice('12', true)
            ->addChoice('5 + 7', true);

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '7 + 7 ='))
            ->addChoice('5', false)
            ->addChoice('14', true);

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '8 + 8 ='))
            ->addChoice('16', true)
            ->addChoice('12', false)
            ->addChoice('9', false)
            ->addChoice('5', false);

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '9 + 9 ='))
            ->addChoice('18', true)
            ->addChoice('9', false)
            ->addChoice('17 + 1', true)
            ->addChoice('2 + 16', true);

        $questions[] = (new \App\TestingSystem\Domain\Model\Question(Id::next(), '10 + 10 ='))
            ->addChoice('0', false)
            ->addChoice('2', false)
            ->addChoice('8', false)
            ->addChoice('20', true);

        $test = new Test(Id::next(), 'Textmagic Test');
        foreach ($questions as $question) {
            $em->persist($question);
            $test->addQuestion($question->getId());
        }
        $em->persist($test);

        $em->flush();

        $output->writeln('Added fixtures...');

        return 0;
    }
}
