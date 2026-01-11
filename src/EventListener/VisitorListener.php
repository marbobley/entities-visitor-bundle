<?php

namespace Marbobley\EntitiesVisitorBundle\EventListener;

use Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation\PersistVisitorInformation;
use Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation\ResolverConcreteVisitorInformation;
use Marbobley\EntitiesVisitorBundle\Model\VisitorInformation as VisitorInformationModel;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

final readonly class VisitorListener
{
    const UNKNOWN = 'Unknown';

    public function __construct(private LoggerInterface $logger,
                                private PersistVisitorInformation $persistVisitorInformation,
                                private ResolverConcreteVisitorInformation $concreteVisitorInformation,
                                #[Autowire(param: 'entities_visitor_bundle.enable')]
                                private bool $enable)
    {
    }

    #[AsEventListener]
    public function onTerminateEvent(TerminateEvent $event): void
    {
        if(!$this->enable)
            return;

        $request = $event->getRequest();

        // Informations de base
        $clientIP = $request->getClientIp() ?? self::UNKNOWN;
        $userAgent = $request->headers->get('User-Agent') ?? self::UNKNOWN;

        // Identifier le contrôleur/action et la route qui ont servi la requête
        // Symfony renseigne ces informations dans les attributs de la Request
        $controller = $request->attributes->get('_controller') ?? self::UNKNOWN;
        $route = $request->attributes->get('_route') ?? self::UNKNOWN;
        $path = $request->getPathInfo();
        $method = $request->getMethod();

        // Log détaillé pour savoir quel contrôleur a déclenché l'action
        $this->logger->debug('Visitor Information', [
            'ip' => $clientIP,
            'method' => $method,
            'path' => $path,
            'route' => $route,
            'controller' => $controller,
            'userAgent' => $userAgent,
        ]);

        $entityClass = $this->concreteVisitorInformation->resolveConcreteVisitorInformationClass();
        if ($entityClass === null) {
            // Aucune entité concrète trouvée dans l'application qui étend le modèle du bundle
            $this->logger->warning('Aucune entité concrete pour VisitorInformation n’a été trouvée. Persistance ignorée.', [
                'expected_parent' => VisitorInformationModel::class,
            ]);
            return;
        }

        $visitor = new $entityClass();
        $visitor->ip = $clientIP;
        $visitor->userAgent = $userAgent;
        $visitor->visitedAt = new DateTimeImmutable();
        $visitor->method = $method;
        $visitor->route = $route;
        $visitor->path = $path;
        $visitor->controller = $controller;
        $this->persistVisitorInformation->save($visitor);
    }
}
