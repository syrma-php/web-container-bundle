<?php

namespace Syrma\WebContainerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Syrma\WebContainer\Executor;
use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerContext;
use Syrma\WebContainer\ServerContextInterface;
use Syrma\WebContainer\ServerInterface;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddRequestHandlerPass;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddServerPass;

/**
 * Run the server.
 */
class ServerRunCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('syrma:web-container:server:run');
        $this->setDescription('Run the syrma server');
        $this->setDefinition(array(
            new InputOption(
                'listenAddress',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Address where the server will listen.',
                ServerContextInterface::DEFAULT_ADDRESS
            ),

            new InputOption(
                'listenPort',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Port where the server will listen.',
                ServerContextInterface::DEFAULT_PORT
            ),

            new InputOption(
                'serverAlias',
                null,
                InputOption::VALUE_OPTIONAL,
                'Alias of the server'
            ),

            new InputOption(
                'requestHandlerAlias',
                null,
                InputOption::VALUE_OPTIONAL,
                'Alias of the requestHandler'
            ),
        ));
    }

    /**
     * @param string|null $alias
     *
     * @return ServerInterface
     */
    protected function getServer($alias)
    {
        $serverRegistry = $this->getContainer()->get(AddServerPass::REGISTRY_ID);

        return empty($alias) ? $serverRegistry->getDefault() : $serverRegistry->get($alias);
    }

    /**
     * @param string|null $alias
     *
     * @return RequestHandlerInterface
     */
    protected function getRequestHandler($alias)
    {
        $reqHandRegistry = $this->getContainer()->get(AddRequestHandlerPass::REGISTRY_ID);

        return empty($alias) ? $reqHandRegistry->getDefault() : $reqHandRegistry->get($alias);
    }

    /**
     * @param string|null $serverAlias
     * @param string|null $reqHandlerAlias
     *
     * @return Executor
     */
    protected function createExecutor($serverAlias, $reqHandlerAlias)
    {
        return new Executor(
            $this->getServer($serverAlias),
            $this->getRequestHandler($reqHandlerAlias)
        );
    }

    /**
     * @param string $listenAddress
     * @param int    $listenPort
     *
     * @return ServerContextInterface
     */
    protected function createContext($listenAddress, $listenPort)
    {
        return new ServerContext($listenAddress, $listenPort);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createExecutor(
            $input->getOption('serverAlias'),
            $input->getOption('requestHandlerAlias')
        )
        ->execute($this->createContext(
                $input->getOption('listenAddress'),
                $input->getOption('listenPort')
            )
        );
    }
}
