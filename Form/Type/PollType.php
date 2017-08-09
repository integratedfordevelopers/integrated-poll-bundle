<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PollBundle\Form\Type;

use Integrated\Bundle\PollBundle\Document\Embedded\Option;
use Integrated\Bundle\PollBundle\Document\Poll;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class PollType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Poll $poll */
        $poll = $options['poll'];

        /** @var Option $option */
        foreach ($poll->getOptions() as $option) {
            $choices[$option->getName()] = $option->getName();
        }

        $choices = array_filter($choices, function ($value) {
            return $value !== null;
        });

        $builder->add(
            'options',
            ChoiceType::class,
            [
                'label' => false,
                'expanded' => true,
                'choices' => $choices
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('poll');
        $resolver->setAllowedTypes('poll', Poll::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_poll';
    }
}
