<?php
namespace Marbobley\EntitiesVisitorBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
final class EntitiesVisitorBundle extends AbstractBundle
{
    protected string $extensionAlias = 'entities_visitor_bundle';
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->booleanNode('enable')->defaultValue(true)
            ->end()
            ->end()
        ;
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver(
            [__DIR__.'/../config/doctrine/mapping' => 'Marbobley\EntitiesVisitorBundle\Model'],
        ));
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
        $container->parameters()->set('entities_visitor_bundle.enable', $config['enable']);
    }
}
