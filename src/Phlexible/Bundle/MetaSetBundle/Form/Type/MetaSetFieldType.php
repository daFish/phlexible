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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Meta set field form type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetFieldType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id');
        $builder->add('name');
        $builder->add('type');
        $builder->add('options');
        $builder->add('synchronized', 'checkbox');
        $builder->add('readonly', 'checkbox');
        $builder->add('required', 'checkbox');

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            // cleanup extjs data - this shouldn't be necessary, try to remove data from extjs
            $data = $event->getData();
            if (isset($data['id'])) {
                unset($data['id']);
            }
            if (isset($data['metaSetId'])) {
                unset($data['metaSetId']);
            }
            $event->setData($data);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'Phlexible\Component\MetaSet\Domain\MetaSetField',
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'metaSetField';
    }
}
