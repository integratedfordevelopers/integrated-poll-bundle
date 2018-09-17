<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\PollBundle\Document;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\PollBundle\Document\Embedded\Option;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * @author Michael Jongman <michael@e-active.nl>
 *
 * @Type\Document("Poll")
 */
class Poll extends Content
{
    /**
     * @var string
     * @Type\Field
     */
    protected $title;

    /**
     * @var Option[]
     * @Type\Field(
     *      type="Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType",
     *      options={
     *          "allow_add"=true,
     *          "allow_delete"=true,
     *          "required"=false,
     *          "entry_type"="Integrated\Bundle\PollBundle\Form\Type\Embedded\OptionType",
     *          "entry_options"={
     *              "data_class"="Integrated\Bundle\PollBundle\Document\Embedded\Option"
     *          },
     *      }
     * )
     */
    protected $options = [];

    /**
     * @var bool
     */
    protected $voted = false;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Option[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Option[] $options
     * @return $this
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param string $name
     */
    public function updateCount($name)
    {
        foreach ($this->options as $option) {
            if ($option->getName() == $name) {
                $option->incrementCount();
            }
        }
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        $cnt = 0;
        foreach ($this->options as $option) {
            $cnt += $option->getCount();
        }

        return $cnt;
    }

    /**
     * @return bool
     */
    public function isVoted()
    {
        return $this->voted;
    }

    /**
     * @param bool $voted
     * @return $this
     */
    public function setVoted($voted = true)
    {
        $this->voted = $voted;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
