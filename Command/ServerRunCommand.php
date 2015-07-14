<?php

namespace Syrma\WebContainerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Syrma\WebContainer\Executor;
use Syrma\WebContainer\ServerContext;

class ServerRunCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('syrma:web-container:server:run');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serverRegistry = $this->getContainer()->get('syrma.web_container.server.registry');
        $server = $serverRegistry->getDefault();

        $requestHandlerRegistry = $this->getContainer()->get('syrma.web_container.request_handler.registry');
        $requestHandler = $requestHandlerRegistry->getDefault();

        $exe = new Executor($server, $requestHandler);
        $exe->execute(new ServerContext());
    }
}
