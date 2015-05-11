<?php

namespace Phlexible\Bundle\QueueBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('command', 'text');
        $builder->add('arguments', 'text');
        $builder->add('priority', 'integer');
        $builder->add('maxRuntime', 'integer');
        $builder->add('maxRuntime', 'integer');
        $builder->add('createdAt', 'datetime');
        $builder->add('executeAfter', 'datetime');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'Phlexible\Bundle\QueueBundle\Entity\Job',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'job';
    }
}