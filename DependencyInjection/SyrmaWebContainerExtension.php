<?php

namespace Syrma\WebContainerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
            ->addTag('syrma.web_container.server', array(
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
            ->addTag('syrma.web_container.request_handler', array(
                    'default' => 'symfony' == $defaultRequestHandler,
                    'alias' => 'symfony',
                )
            )
        ;
    }
}
