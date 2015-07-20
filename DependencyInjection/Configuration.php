<?php

namespace Syrma\WebContainerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Syrma\WebContainer\Server\Swoole\SwooleServer;

/**
 * Bundle config.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('syrma_web_container');

        $this->addServerSection($rootNode);
        $this->addRequestHandlerSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addServerSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()

                ->arrayNode('server')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')
                            ->info('Alias of the default server')
                            ->defaultValue(SwooleServer::isAvaiable() ? 'swoole' : null)
                        ->end()
                        ->append($this->addSwooleServerNode())
                    ->end()
                ->end()

            ->end()
        ;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function addSwooleServerNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('swoole');

        $node
            ->canBeEnabled()
            ->children()
                ->arrayNode('transformer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('use_server_request')
                            ->defaultTrue()
                        ->end()
                        ->integerNode('response_buffer')
                            ->defaultValue(8096)
                            ->min(1)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('options')
                    ->useAttributeAsKey('name')
                    ->prototype('variable')->end()
                    ->info('Options of SwooleServer. @see SwooleServerOptions - Todo - make config')
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addRequestHandlerSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('request_handler')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')
                            ->info('Alias of the default requestHandler')
                            ->defaultValue('symfony')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
