<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PollBundle\Controller\Content;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\BlockBundle\Templating\BlockManager;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PollBundle\Form\Type\PollType;
use Integrated\Bundle\PollBundle\Event\PollEvent;
use Integrated\Bundle\PollBundle\Document\Poll;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PollController
{
    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @var BlockManager
     */
    protected $blockManager;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param TwigEngine $templating
     * @param ThemeManager $themeManager
     * @param BlockManager $blockManager
     * @param DocumentManager $documentManager
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        TwigEngine $templating,
        ThemeManager $themeManager,
        BlockManager $blockManager,
        FormFactory $formFactory,
        DocumentManager $documentManager,
        EventDispatcher $eventDispatcher
    ) {
        $this->templating = $templating;
        $this->themeManager = $themeManager;
        $this->blockManager = $blockManager;
        $this->formFactory = $formFactory;
        $this->documentManager = $documentManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ContentTypePage $page
     * @param Poll $poll
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ContentTypePage $page, Poll $poll, Request $request)
    {
        $this->blockManager->setDocument($poll);

        $form = $this->createForm($poll);

        $this->eventDispatcher->dispatch(PollEvent::INIT, new PollEvent($poll, $form->getData()));

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->eventDispatcher->dispatch(PollEvent::VOTE, new PollEvent($poll, $form->getData()));
                $this->documentManager->flush($poll);
            }
        }

        return $this->templating->renderResponse(
            $this->themeManager->locateTemplate('content/Poll/show/' . $page->getLayout()),
            [
                'page' => $page,
                'form' => $form->createView(),
                'poll' => $poll,
            ]
        );
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
