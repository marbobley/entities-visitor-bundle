<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Marbobley\EntitiesVisitorBundle\VisitorCounter;
use Marbobley\EntitiesVisitorBundle\EventListener\VisitorListener as BundleVisitorListener;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $services
        ->set('nora.visitor_counter', VisitorCounter::class)
        ->alias(VisitorCounter::class, 'nora.visitor_counter')
    ;

    // Register the VisitorListener from the bundle so its AsEventListener attribute is processed
    $services
        ->set(BundleVisitorListener::class)
    ;
};
