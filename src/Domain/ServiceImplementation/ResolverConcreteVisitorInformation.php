<?php

namespace Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation;

use Doctrine\ORM\EntityManagerInterface;
use Marbobley\EntitiesVisitorBundle\Model\VisitorInformation as VisitorInformationModel;

class ResolverConcreteVisitorInformation
{


    public function __construct(private EntityManagerInterface $manager)
    {
    }
    function resolveConcreteVisitorInformationClass(): ?string
    {
        $metadataFactory = $this->manager->getMetadataFactory();
        foreach ($metadataFactory->getAllMetadata() as $metadata) {
            $className = $metadata->getName();
            if (is_subclass_of($className, VisitorInformationModel::class)) {
                return $className;
            }
        }

        return null;
    }
}
