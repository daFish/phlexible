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
 * Video template form type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoTemplateType extends MediaTemplateType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('matchFormat', 'checkbox');
        $builder->add('forWeb', 'checkbox');
        $builder->add('format', 'text');
        $builder->add('deinterlace', 'checkbox');
        $builder->add('resizeMethod', 'text');
        $builder->add('videoWidth', 'integer');
        $builder->add('videoHeight', 'integer');
        $builder->add('videoFormat', 'text');
        $builder->add('videoBitrate', 'text');
        $builder->add('videoFramerate', 'text');
        $builder->add('audioFormat', 'text');
        $builder->add('audioBitrate', 'text');
        $builder->add('audioSamplerate', 'text');
        $builder->add('audioSamplebits', 'text');
        $builder->add('audioChannels', 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'Phlexible\Component\MediaTemplate\Domain\VideoTemplate',
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
