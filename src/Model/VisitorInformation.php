<?php

namespace Marbobley\EntitiesVisitorBundle\Model;

abstract class VisitorInformation{
    public string $ip;
    public string $userAgent;
    public \DateTimeImmutable $visitedAt;

    public string $method;
    public string $path;
    public string $route;
    public string $controller;


}
