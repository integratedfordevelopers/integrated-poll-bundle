<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PollBundle\Document\Block;

use Symfony\Component\Validator\Constraints as Assert;

use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\BlockBundle\Document\Block\PublishTitleTrait;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * PollBlock document
 *
 * @author Michael Jongman <michael@e-active.nl>
 *
 * @Type\Document("Poll block")
 */
class PollBlock extends Block
{
    use PublishTitleTrait;

    /**
     * @var string
     * @Assert\NotBlank
     * @Type\Field(
     *       options={
     *          "attr"={"class"="main-title"}
     *       }
     * )
     */
    protected $title;

    /**
     * @var bool
     * @Type\Field(
     *      type="Symfony\Component\Form\Extension\Core\Type\CheckboxType",
     *      options={
     *          "required"=false,
     *          "attr"={"align_with_widget"=true}
     *      }
     * )
     */
    protected $showVotes = false;

    /**
     * @return bool
     */
    public function getShowVotes()
    {
        return $this->showVotes;
    }

    /**
     * @param bool $showVotes
     * @return $this
     */
    public function setShowVotes($showVotes)
    {
        $this->showVotes = $showVotes;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'poll';
    }
}
