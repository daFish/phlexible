<?php

namespace Phlexible\Bundle\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userId', 'text');
        $builder->add('private', 'checkbox');
        $builder->add('title', 'text');
        $builder->add('createdAt', 'datetime');
        $builder->add('modifiedAt', 'datetime');
        $builder->add('expression', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'Phlexible\Bundle\MessageBundle\Entity\Filter',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'message_filter';
    }
}