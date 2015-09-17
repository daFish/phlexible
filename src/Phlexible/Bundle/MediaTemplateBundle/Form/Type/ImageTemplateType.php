<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Image template form type.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageTemplateType extends MediaTemplateType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('width', 'integer');
        $builder->add('height', 'integer');
        $builder->add('method', 'text');
        $builder->add('scale', 'text');
        $builder->add('forWeb', 'checkbox');
        $builder->add('format', 'text');
        $builder->add('colorspace', 'text');
        $builder->add('tiffCompression', 'text');
        $builder->add('depth', 'text');
        $builder->add('quality', 'integer');
        $builder->add('backgroundcolor', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Phlexible\Component\MediaTemplate\Domain\ImageTemplate',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mediaTemplate';
    }
}
