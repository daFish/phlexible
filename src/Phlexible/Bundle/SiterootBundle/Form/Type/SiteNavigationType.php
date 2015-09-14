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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Site navigation form type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteNavigationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('nodeId', 'integer');
        $builder->add('maxDepth', 'integer');

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            // cleanup extjs data - this shouldn't be necessary, try to remove data from extjs
            $data = $event->getData();
            if (isset($data['id'])) {
                unset($data['id']);
            }
            if (isset($data['siteId'])) {
                unset($data['siteId']);
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
            'data_class'        => 'Phlexible\Component\Site\Domain\Navigation',
            'csrf_protection'   => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_navigation';
    }
}
