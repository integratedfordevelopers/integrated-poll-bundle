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
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

use Integrated\Bundle\PollBundle\Event\PollEvent;
use Integrated\Bundle\PollBundle\Document\Poll;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PollCookieSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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

        if (!$request instanceof Request) {
            return;
        }

        $poll = $event->getPoll();

        if ($request->cookies->has($this->getCookieId($poll))) {
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

        if (!$request instanceof Request) {
            return;
        }

        $data = $event->getData();

        if (!isset($data['options'])) {
            return;
        }

        $cookie = new Cookie($this->getCookieId($poll), $data['options'], time() + 3600 * 24 * 7);

        $response = new Response();
        $response->headers->setCookie($cookie);
        $response->prepare($request);
        $response->sendHeaders();

        $poll->updateCount($data['options']);
    }

    /**
     * @param Poll $poll
     * @return string
     */
    protected function getCookieId(Poll $poll)
    {
        return 'poll_' . $poll->getId();
    }
}
