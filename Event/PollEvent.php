<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PollBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Integrated\Bundle\PollBundle\Document\Poll;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PollEvent extends Event
{
    /**
     * Called during the initialization of the poll
     */
    const INIT = 'poll.init';

    /**
     * Called on the poll vote
     */
    const VOTE = 'poll.vote';

    /**
     * @var Poll
     */
    protected $poll;

    /**
     * @var array|object
     */
    protected $data;

    /**
     * @param Poll $poll
     * @param array|object $data
     */
    public function __construct(Poll $poll, $data = null)
    {
        $this->poll = $poll;
        $this->data = $data;
    }

    /**
     * @return Poll
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }
}
