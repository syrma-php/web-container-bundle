<?php

namespace Syrma\WebContainerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Syrma\WebContainer\Server\Swoole\SwooleServer;
use Syrma\WebContainer\Util\ErrorPageLoader;
use Syrma\WebContainerBundle\Executor\ExecutorFactory;

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
        $this->addExceptionHandlerSection($rootNode);
        $this->addExecutorSection($rootNode);

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

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addExceptionHandlerSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('exception_handler')
                    ->addDefaultsIfNotSet()
                    ->children()

                        ->arrayNode('error_page')
                            ->addDefaultsIfNotSet()
                            ->children()

                                ->scalarNode('template_path')
                                    ->info('Path of the error page templates')
                                    ->defaultValue(ErrorPageLoader::getDefaultTemplatePath())
                                ->end()

                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addExecutorSection(ArrayNodeDefinition $rootNode)
    {
        $defaultExceptionHandlerService = 'syrma.web_container.exception_handler';
        $defaultParentService = 'syrma.web_container.executor.abstract';

        $rootNode
            ->children()
                ->arrayNode('executor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')
                            ->info('Alias of the default requestHandler')
                            ->defaultValue('default')
                        ->end()
                        ->arrayNode('executorList')
                            ->defaultValue(array(
                                'default' => array(
                                    'server' => ExecutorFactory::USE_DEFAULT_KEY,
                                    'request_handler' => ExecutorFactory::USE_DEFAULT_KEY,
                                    'exception_handler_service' => $defaultExceptionHandlerService,
                                    'parent_service' => $defaultParentService,
                                    'use_factory' => true,
                                ),
                            ))
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('server')
                                        ->info('Alias of the server or identifier of server')
                                        ->defaultValue(ExecutorFactory::USE_DEFAULT_KEY)
                                    ->end()
                                    ->scalarNode('request_handler')
                                        ->info('Alias of the requestHandler or identifier of request handler')
                                        ->defaultValue(ExecutorFactory::USE_DEFAULT_KEY)
                                    ->end()
                                    ->scalarNode('exception_handler_service')
                                        ->info('Identifier of the error handler service')
                                        ->defaultValue($defaultExceptionHandlerService)
                                    ->end()
                                    ->scalarNode('parent_service')
                                        ->info('Identifier of the error handler service')
                                        ->defaultValue($defaultParentService)
                                    ->end()
                                    ->booleanNode('use_factory')
                                        ->info('Is it uses the Executor factory')
                                        ->defaultTrue()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
