<?php

namespace Syrma\WebContainerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Syrma\WebContainer\Executor;
use Syrma\WebContainer\ServerContext;
use Syrma\WebContainer\ServerContextInterface;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddExecutorPass;

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
                'executor',
                'x',
                InputOption::VALUE_OPTIONAL,
                'Alias of the executor'
            ),

        ));
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
     * @param string|null $alias
     *
     * @return Executor
     */
    private function getExecutor($alias)
    {
        $registry = $this->getContainer()->get(AddExecutorPass::REGISTRY_ID);

        return empty($alias) ? $registry->getDefault() : $registry->get($alias);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $executor = $this->getExecutor($input->getOption('executor'));
        $executor->execute($this->createContext(
                $input->getOption('listenAddress'),
                $input->getOption('listenPort')
            )
        );
    }
}
