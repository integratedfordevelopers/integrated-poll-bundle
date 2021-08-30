<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PollBundle\Entity;

use Darsyn\IP\Version\Multi as IP;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PollIp
{
    /**
     * @var string
     */
    protected $poll;

    /**
     * @var IP
     */
    protected $ip;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @return string
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * @param string $poll
     * @return $this
     */
    public function setPoll($poll)
    {
        $this->poll = $poll;
        return $this;
    }

    /**
     * @return IP
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param IP $ip
     * @return $this
     */
    public function setIp(IP $ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return $this
     */
    public function incrementCount()
    {
        $this->count++;
        return $this;
    }
}
