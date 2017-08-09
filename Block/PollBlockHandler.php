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
use Integrated\Bundle\PollBundle\Form\Type\PollType;

use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Common\Block\BlockInterface;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

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
     * @param FormFactory $formFactory
     * @param DocumentManager $documentManager
     * @param RequestStack $requestStack
     */
    public function __construct(
        FormFactory $formFactory,
        DocumentManager $documentManager,
        RequestStack $requestStack
    ) {
        $this->formFactory = $formFactory;
        $this->documentManager = $documentManager;
        $this->requestStack = $requestStack;
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

        $content = $this->getItem();

        $form = $this->createForm($content);

        $response = new Response();
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                $content->updateCount($data['options']);

                $this->documentManager->flush($content);

                $cookie = new Cookie($content->getId(), $data['options'], time() + 3600 * 24 * 7);
                $response->headers->setCookie($cookie);
                $response->prepare($request);
                $response->sendHeaders();
            }
        }



        return $this->render([
                'block' => $block,
                'form' => $form->createView(),
                'content' => $content
            ]);
    }

    /**
     * @param Poll $poll
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createForm(Poll $poll)
    {
        $form = $this->formFactory->createBuilder(PollType::class, null, ['method' => 'post', 'poll' => $poll]);

        return $form->getForm();
    }

    /**
     * @return array|null|Poll
     */
    public function getItem()
    {
        return $this->documentManager->createQueryBuilder(Poll::class)
            ->sort('createdAt', 'DESC')
            ->getQuery()
            ->getSingleResult();
    }
}
