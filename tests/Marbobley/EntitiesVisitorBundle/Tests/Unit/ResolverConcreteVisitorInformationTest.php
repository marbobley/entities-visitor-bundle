<?php

namespace Marbobley\EntitiesVisitorBundle\Tests\Unit;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation\ResolverConcreteVisitorInformation;
use Marbobley\EntitiesVisitorBundle\Model\VisitorInformation;
use PHPUnit\Framework\TestCase;

class ResolverConcreteVisitorInformationTest extends TestCase
{
    public function testResolveConcreteVisitorInformationClassReturnsNullIfNoSubclassFound()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $metadataFactory = $this->createMock(ClassMetadataFactory::class);

        $entityManager->method('getMetadataFactory')->willReturn($metadataFactory);
        $metadataFactory->method('getAllMetadata')->willReturn([]);

        $resolver = new ResolverConcreteVisitorInformation($entityManager);
        $result = $resolver->resolveConcreteVisitorInformationClass();

        $this->assertNull($result);
    }

    public function testResolveConcreteVisitorInformationClassReturnsClassNameIfSubclassFound()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $metadataFactory = $this->createMock(ClassMetadataFactory::class);

        $metadata = $this->createMock(\Doctrine\ORM\Mapping\ClassMetadata::class);
        $metadata->method('getName')->willReturn(ConcreteVisitorInformation::class);

        $entityManager->method('getMetadataFactory')->willReturn($metadataFactory);
        $metadataFactory->method('getAllMetadata')->willReturn([$metadata]);

        $resolver = new ResolverConcreteVisitorInformation($entityManager);
        $result = $resolver->resolveConcreteVisitorInformationClass();

        $this->assertEquals(ConcreteVisitorInformation::class, $result);
    }
}

class ConcreteVisitorInformation extends VisitorInformation {}
