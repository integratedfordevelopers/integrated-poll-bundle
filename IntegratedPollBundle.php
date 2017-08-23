<?php

namespace Integrated\Bundle\PollBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Integrated\Bundle\PollBundle\DependencyInjection\Compiler\ThemeManagerPass;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class IntegratedPollBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ThemeManagerPass());

        $container->addCompilerPass(
            new RegisterListenersPass(
                'integrated_poll.event_dispatcher',
                'integrated_poll.event_listener',
                'integrated_poll.event_subscriber'
            )
        );
    }
}
