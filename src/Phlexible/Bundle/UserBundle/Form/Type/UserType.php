<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * User form type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname');
        $builder->add('lastname');
        $builder->add('comment');
        $builder->add('username');
        $builder->add('email', 'email');
        $builder->add('plainPassword', 'password');
        $builder->add('confirmationToken');
        $builder->add('expired', 'checkbox');
        $builder->add('enabled', 'checkbox');
        $builder->add('locked', 'checkbox');
        $builder->add('properties');
        $builder->add('roles');
        $builder->add('groups');
        $builder->add('credentialsExpired', 'checkbox');
        $builder->add('credentialsExpireAt', 'datetime', array(
            'widget' => 'single_text',
        ));
        $builder->add('passwordRequestedAt', 'datetime', array(
            'widget' => 'single_text',
        ));
        #$builder->add('lastLogin', 'datetime');
        $builder->add('expiresAt', 'datetime', array(
            'widget' => 'single_text',
        ));
        $builder->add('createdAt', 'datetime', array(
            'widget' => 'single_text',
        ));
        $builder->add('modifiedAt', 'datetime', array(
            'widget' => 'single_text',
        ));
        #$builder->add('extra');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Phlexible\Bundle\UserBundle\Entity\User',
            'csrf_protection'   => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }
}
