<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('default', 'checkbox');
        $builder->add('hostname', 'text');
        $builder->add('createdBy', 'text');
        $builder->add('createdAt', 'datetime', array(
            'widget' => 'single_text',
            'input' => 'datetime'
        ));
        $builder->add('modifiedBy', 'text');
        $builder->add('modifiedAt', 'datetime', array(
            'widget' => 'single_text',
            'input' => 'datetime'
        ));
        $builder->add('titles', 'collection', array(
            'type'         => 'text',
            'allow_add'    => true,
            #'allow_delete' => true,
            'by_reference' => false,
            #'delete_empty' => true,
        ));
        $builder->add('properties', 'collection', array(
            'type'         => 'text',
            'allow_add'    => true,
            #'allow_delete' => true,
            'by_reference' => false,
            #'delete_empty' => true,
        ));
        $builder->add('nodeAliases', 'collection', array(
            'type'         => new SiteNodeAliasType(),
            'allow_add'    => true,
            #'allow_delete' => true,
            'by_reference' => false,
            #'delete_empty' => true,
        ));
        $builder->add('nodeConstraints', 'collection', array(
            'type'         => new SiteNodeConstraintsType(),
            'allow_add'    => true,
            #'allow_delete' => true,
            'by_reference' => false,
            #'delete_empty' => true,
        ));
        $builder->add('navigations', 'collection', array(
            'type'         => new SiteNavigationType(),
            'allow_add'    => true,
            #'allow_delete' => true,
            'by_reference' => false,
            #'delete_empty' => true,
        ));
        $builder->add('entryPoints', 'collection', array(
            'type'         => new SiteEntryPointType(),
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
            'data_class'        => 'Phlexible\Component\Site\Domain\Site',
            'csrf_protection'   => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site';
    }
}
