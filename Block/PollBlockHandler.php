<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PollBundle\Block;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\PollBundle\Document\Block\PollBlock;
use Integrated\Bundle\PollBundle\Document\Poll;
use Integrated\Bundle\PollBundle\Event\PollEvent;
use Integrated\Bundle\PollBundle\Form\Type\PollType;
use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Common\Block\BlockInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Poll block handler
 *
 * @author Michael Jongman <michael@e-active.nl>
 */
class PollBlockHandler extends BlockHandler
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param FormFactory $formFactory
     * @param DocumentManager $documentManager
     * @param RequestStack $requestStack
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        FormFactory $formFactory,
        DocumentManager $documentManager,
        RequestStack $requestStack,
        EventDispatcher $eventDispatcher
    ) {
        $this->formFactory = $formFactory;
        $this->documentManager = $documentManager;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, array $options)
    {
        if (!$block instanceof PollBlock) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return;
        }

        $poll = $this->documentManager->getRepository(Poll::class)->findLatestPoll();

        if (!$poll instanceof Poll) {
            return;
        }

        $form = $this->createForm($poll);

        $this->eventDispatcher->dispatch(PollEvent::INIT, new PollEvent($poll, $form->getData()));

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->eventDispatcher->dispatch(PollEvent::VOTE, new PollEvent($poll, $form->getData()));
                $this->documentManager->flush($poll);
            }
        }

        return $this->render([
            'block' => $block,
            'form'  => $form->createView(),
            'poll'  => $poll
        ]);
    }

    /**
     * @param Poll $poll
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createForm(Poll $poll)
    {
        return $this->formFactory->create(PollType::class, null, ['poll' => $poll]);
    }
}
