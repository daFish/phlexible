<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Task\Type;

/**
 * General element task type.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GeneralType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'element.general';
    }

    /**
     * Get required parameters for this task.
     *
     * @return array
     */
    public function getRequiredParameters()
    {
        return array('type', 'type_id');
    }

    /**
     * @return string
     */
    protected function getTitleKey()
    {
        return 'elements.task_general';
    }

    /**
     * @return string
     */
    protected function getTextKey()
    {
        return 'elements.task_general_template';
    }
}
