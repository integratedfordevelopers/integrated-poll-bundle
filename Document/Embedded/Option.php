<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\PollBundle\Document\Embedded;

use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * @author Michael Jongman <michael@e-active.nl>
 *
 * @Type\Document("Option")
 */
class Option
{
    /**
     * @var string
     * @Type\Field
     */
    protected $name;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
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
