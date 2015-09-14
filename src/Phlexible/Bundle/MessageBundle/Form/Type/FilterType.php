<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Form\Type;

use Phlexible\Component\Expression\Transformer\ExpressionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Filter form type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateOptions = array(
            'widget' => 'single_text',
            'date_format' => 'yyyy-MM-dd HH:mm:ss',
            'format' => 'yyyy-MM-dd HH:mm:ss'
        );

        $builder->add('id', 'text');
        $builder->add('userId', 'text');
        $builder->add('private', 'checkbox');
        $builder->add('title', 'text');
        $builder->add('createdAt', 'datetime', $dateOptions);
        $builder->add('modifiedAt', 'datetime', $dateOptions);
        $builder->add(
            $builder->create('expression', 'text')
                ->addModelTransformer(new ExpressionTransformer())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'Phlexible\Component\MessageFilter\Domain\Filter',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filter';
    }
}
