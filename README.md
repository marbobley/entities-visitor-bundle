# entities-visitor-bundle
Package to manage visitors with DB persistence in Symfony

## Installation
composer require marbobley/entities-visitor-bundle

## Usage 
Create an entity Visitor and extends it from VisitorInformation
* Create the migration script: symfony console make:migration
* Migrate the script : symfony console doctrine:migrations:migrate

When someone visits your site, the event listener will create an entry into the VisitorInformation table

## Features
Currently, the bundle only supports Doctrine ORM.
Column saved : 
* client ip
* user Agent
* visited at 
* method 
* route
* control
* path

## Example 
````
<?php

namespace App\Entity;

use App\Repository\VisitorInformationRepository;
use Doctrine\ORM\Mapping as ORM;
use Marbobley\EntitiesVisitorBundle\Model\VisitorInformation as VisitorInformationModel;

#[ORM\Entity(repositoryClass: VisitorInformationRepository::class)]
class VisitorInformation extends VisitorInformationModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
````
