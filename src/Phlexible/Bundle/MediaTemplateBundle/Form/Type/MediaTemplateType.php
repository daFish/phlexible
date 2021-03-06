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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Media template form type.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class MediaTemplateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('key', 'text');
        $builder->add('type', 'text');
        $builder->add('system', 'checkbox');
        $builder->add('cache', 'checkbox');
        $builder->add('revision', 'integer');
        $builder->add('storage', 'text');
        $builder->add('createdAt', 'datetime', array(
            'widget' => 'single_text',
            'input' => 'datetime',
        ));
        $builder->add('modifiedAt', 'datetime', array(
            'widget' => 'single_text',
            'input' => 'datetime',
        ));
    }
}
