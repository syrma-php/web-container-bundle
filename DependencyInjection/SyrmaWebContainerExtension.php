<?php

namespace Syrma\WebContainerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddExecutorPass;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddRequestHandlerPass;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddServerPass;

class SyrmaWebContainerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerServerConfiguration(
            isset($config['server']) ? $config['server'] : array(),
            $container,
            $loader
        );

        $this->registerRequestHandlerConfiguration(
            isset($config['request_handler']) ? $config['request_handler'] : array(),
            $container,
            $loader
        );

        $this->registerExceptionHandlerConfiguration(
            isset($config['exception_handler']) ? $config['exception_handler'] : array(),
            $container,
            $loader
        );

        $this->registerExecutorConfiguration(
            isset($config['executor']) ? $config['executor'] : array(),
            $container,
            $loader
        );
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function registerServerConfiguration(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        $loader->load('server.xml');

        $defaultServer = isset($config['default']) ? $config['default'] : null;

        if (!empty($config['swoole']['enabled'])) {
            $this->registerSwooleServerConfiguration($config['swoole'], $container, $loader, $defaultServer);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     * @param string           $defaultServer
     */
    private function registerSwooleServerConfiguration(array $config, ContainerBuilder $container, LoaderInterface $loader, $defaultServer)
    {
        $loader->load('server.swoole.xml');
        if (!empty($config['transformer'])) {
            $container->findDefinition('syrma.web_container.server.swoole.message_transformer')
                ->replaceArgument(1, (bool) $config['transformer']['use_server_request'])
                ->replaceArgument(2, (int) $config['transformer']['response_buffer'])
            ;
        }

        if (!empty($config['options'])) {
            $container->findDefinition('syrma.web_container.server.swoole.options')
                ->replaceArgument(0, (array) $config['options'])
            ;
        }

        $container->findDefinition('syrma.web_container.server.swoole')
            ->addTag(AddServerPass::TAG_NAME, array(
                    'default' => 'swoole' == $defaultServer,
                    'alias' => 'swoole',
                )
            )
        ;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function registerRequestHandlerConfiguration(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        $loader->load('request_handler.xml');

        $defaultRequestHandler = isset($config['default']) ? $config['default'] : null;

        $container->findDefinition('syrma.web_container.request_handler.symfony')
            ->addTag(AddRequestHandlerPass::TAG_NAME, array(
                    'default' => 'symfony' == $defaultRequestHandler,
                    'alias' => 'symfony',
                )
            )
        ;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function registerExceptionHandlerConfiguration(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        $loader->load('exception_handler.xml');

        if (isset($config['error_page']['template_path'])) {
            $container->findDefinition('syrma.web_container.error_page_loader')
                ->replaceArgument(0, $config['error_page']['template_path']);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function registerExecutorConfiguration(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        $loader->load('executor.xml');

        $defaultExecutor = isset($config['default']) ? $config['default'] : null;

        foreach ($config['executorList'] as $name => $executorConf) {
            $def = new DefinitionDecorator($executorConf['parent_service']);
            $def
                ->replaceArgument(0, $executorConf['server'])
                ->replaceArgument(1, $executorConf['request_handler'])
                ->replaceArgument(2, new Reference($executorConf['exception_handler_service']))
                ->addTag(AddExecutorPass::TAG_NAME, array(
                    'default' => $name == $defaultExecutor,
                    'alias' => $name,
                ))
            ;

            if (!empty($executorConf['use_factory'])) {
                $def->setFactoryService(new Reference('syrma.web_container.executor.factory'));
                $def->setFactoryMethod('create');
            }

            $container->setDefinition(
                sprintf('syrma.web_container.executor.dynamic.%s', $name),
                $def
            );
        }
    }
}
