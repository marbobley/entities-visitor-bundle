<?php

namespace Marbobley\EntitiesVisitorBundle\Tests\Unit;

use Doctrine\ORM\EntityManagerInterface;
use Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation\PersistVisitorInformation;
use Marbobley\EntitiesVisitorBundle\Model\VisitorInformation;
use PHPUnit\Framework\TestCase;

class PersistVisitorInformationTest extends TestCase
{
    public function testSave()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $visitor = new class extends VisitorInformation {};

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($visitor);

        $entityManager->expects($this->once())
            ->method('flush');

        $service = new PersistVisitorInformation($entityManager);
        $service->save($visitor);
    }
}
