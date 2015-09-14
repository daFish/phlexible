<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MetaSetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Meta set form type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('id');
        $builder->add('name');
        $builder->add('revision', 'integer');
        $builder->add('createdBy');
        $builder->add('createdAt', 'datetime', array(
            'widget' => 'single_text',
            'input' => 'datetime'
        ));
        $builder->add('modifiedBy');
        $builder->add('modifiedAt', 'datetime', array(
            'widget' => 'single_text',
            'input' => 'datetime'
        ));
        $builder->add('fields', 'collection', array(
            'type'         => new MetaSetFieldType(),
            'allow_add'    => true,
            #'allow_delete' => true,
            'by_reference' => false,
            #'delete_empty' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'Phlexible\Component\MetaSet\Domain\MetaSet',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'metaSet';
    }
}
