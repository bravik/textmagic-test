<?php

declare(strict_types=1);

namespace App\TestingSystem\Infrastructure\Doctrine;

use App\TestingSystem\Application\FlusherInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class Flusher implements FlusherInterface
{
    private ManagerRegistry $doctrine;
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger   = $logger;
    }

    public function flush(?string $className = null): void
    {
        $em = $className !== null ? $this->doctrine->getManagerForClass($className) : $this->doctrine->getManager();

        if (!$em) {
            $message = $className !== null
                ? "Failed to get entity manager for class $className"
                : 'Failed to get default entity manager'
            ;

            $this->logger->error($message);

            throw new RuntimeException($message);
        }

        $em->flush();
    }
}
