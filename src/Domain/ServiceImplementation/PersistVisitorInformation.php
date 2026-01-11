<?php

namespace Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation;

use Doctrine\ORM\EntityManagerInterface;
use Marbobley\EntitiesVisitorBundle\Model\VisitorInformation;

class PersistVisitorInformation
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function save(VisitorInformation $visitor): void
    {
        $this->manager->persist($visitor);
        $this->manager->flush();
    }
}
