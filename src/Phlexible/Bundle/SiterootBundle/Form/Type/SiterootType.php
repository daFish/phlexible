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

class SiterootType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('default', 'checkbox');
        $builder->add('titles', 'collection', array(
            'type'         => 'text',
            'allow_add'    => true,
            #'allow_delete' => true,
            'by_reference' => false,
            #'delete_empty' => true,
        ));
        $builder->add('specialTids', 'collection', array(
            'type'         => new SiterootSpecialTidType(),
            'allow_add'    => true,
            #'allow_delete' => true,
            'by_reference' => false,
            #'delete_empty' => true,
        ));
        $builder->add('navigations', 'collection', array(
            'type'         => new SiterootNavigationType(),
            'allow_add'    => true,
            #'allow_delete' => true,
            'by_reference' => false,
            #'delete_empty' => true,
        ));
        $builder->add('urls', 'collection', array(
            'type'         => new SiterootUrlType(),
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
            'data_class'        => 'Phlexible\Bundle\SiterootBundle\Entity\Siteroot',
            'csrf_protection'   => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'siteroot';
    }
}
