<?php

namespace Marbobley\EntitiesVisitorBundle\EventListener;

use Marbobley\EntitiesVisitorBundle\Model\VisitorInformation as VisitorInformationModel;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

final readonly class VisitorListener
{
    const UNKNOWN = 'Unknown';

    public function __construct(private LoggerInterface $logger, private EntityManagerInterface $manager)
    {
    }

    #[AsEventListener]
    public function onTerminateEvent(TerminateEvent $event): void
    {
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

        $entityClass = $this->resolveConcreteVisitorInformationClass();
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
        $this->manager->persist($visitor);
        $this->manager->flush();
    }

    /**
     * Trouve la classe d’entité (côté application) qui étend le modèle
     * Marbobley\EntitiesVisitorBundle\Model\VisitorInformation, sans créer de
     * dépendance directe vers App\Entity.
     */
    private function resolveConcreteVisitorInformationClass(): ?string
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
