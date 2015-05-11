<?php

namespace Phlexible\Bundle\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subject', 'text');
        $builder->add('body', 'text');
        $builder->add('type', 'integer');
        $builder->add('channel', 'text');
        $builder->add('role', 'text');
        $builder->add('user', 'text');
        $builder->add('createdAt', 'datetime');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'Phlexible\Bundle\MessageBundle\Entity\Message',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'message';
    }
}