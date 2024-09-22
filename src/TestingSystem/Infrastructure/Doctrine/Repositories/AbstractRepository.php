<?php

declare(strict_types=1);

namespace App\TestingSystem\Infrastructure\Doctrine\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

/**
 * @psalm-template T of object
 */
abstract class AbstractRepository
{
    /**
     * @param class-string<T> $class
     */
    public function __construct(
        private readonly string $class,
        private readonly ManagerRegistry $doctrine,
    ) {
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        $manager = $this->doctrine->getManager($this->doctrine->getDefaultManagerName());
        Assert::isInstanceOf($manager, EntityManagerInterface::class);

        return $manager;
    }

    /**
     * @return EntityRepository<T>
     */
    protected function getRepository(): EntityRepository
    {
        $manager = $this->getEntityManager();

        return new EntityRepository($manager, $manager->getClassMetadata($this->class));
    }
}
