<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PollBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ORM\EntityManager;
use Darsyn\IP\IP;

use Integrated\Bundle\PollBundle\Event\PollEvent;
use Integrated\Bundle\PollBundle\Entity\PollIp;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PollIpSubscriber implements EventSubscriberInterface
{
    /**
     * Maximum of 5 votes from the same IP
     */
    const LIMIT = 5;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param RequestStack $requestStack
     * @param EntityManager $entityManager
     */
    public function __construct(RequestStack $requestStack, EntityManager $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PollEvent::INIT => 'init',
            PollEvent::VOTE => 'vote',
        ];
    }

    /**
     * @param PollEvent $event
     */
    public function init(PollEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request || !$request->getClientIp()) {
            return;
        }

        $poll = $event->getPoll();

        $log = $this->getLog($poll->getId(), $request->getClientIp());

        if ($log && $log->getCount() >= self::LIMIT) {
            $poll->setVoted(); // Unable to vote anymore
        }
    }

    /**
     * @param PollEvent $event
     */
    public function vote(PollEvent $event)
    {
        $poll = $event->getPoll();

        if ($poll->isVoted()) {
            return; // Ignore
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request || !$request->getClientIp()) {
            return;
        }

        $poll = $event->getPoll();

        $log = $this->getLog($poll->getId(), $request->getClientIp(), true);
        $log->incrementCount();

        $this->entityManager->flush($log);
    }

    /**
     * @param string $poll
     * @param string $ip
     * @param bool $create
     * @return PollIp
     */
    protected function getLog($poll, $ip, $create = false)
    {
        $ip = new IP($ip);

        if (!$log = $this->entityManager->getRepository(PollIp::class)->findOneBy(['poll' => $poll, 'ip' => $ip])) {
            if ($create) {
                $log = new PollIp();

                $log->setPoll($poll);
                $log->setIp($ip);

                $this->entityManager->persist($log);
            }
        }

        return $log;
    }
}
