<?php

namespace Marbobley\EntitiesVisitorBundle\Model;

abstract class VisitorInformation{
    public string $ip;
    public string $userAgent;
    public \DateTimeImmutable $visitedAt;


}
