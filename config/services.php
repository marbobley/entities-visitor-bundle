<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;
use Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation\PersistVisitorInformation;
use Marbobley\EntitiesVisitorBundle\Domain\ServiceImplementation\ResolverConcreteVisitorInformation;
use Marbobley\EntitiesVisitorBundle\EventListener\VisitorListener as BundleVisitorListener;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $services
        ->set('marbobley.resolver_concrete_visitor_class', ResolverConcreteVisitorInformation::class)
        ->alias(ResolverConcreteVisitorInformation::class, 'marbobley.resolver_concrete_visitor_class')
    ;

    $services
        ->set('marbobley.persist_visitor_class', PersistVisitorInformation::class)
        ->alias(PersistVisitorInformation::class, 'marbobley.persist_visitor_class');


    // Register the VisitorListener from the bundle so its AsEventListener attribute is processed
    $services
        ->set(BundleVisitorListener::class)
    ;
};
