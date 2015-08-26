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
 * Delete element task type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'element.delete';
    }

    /**
     * Get required parameters for this task
     *
     * @return array
     */
    public function getRequiredParameters()
    {
        return array('type', 'type_id');
    }

    /**
     * Return the task resource
     *
     * @return string
     */
    public function getResource()
    {
        return 'elements_delete';
    }

    /**
     * @return string
     */
    protected function getTitleKey()
    {
        return 'elements.task_delete_element';
    }

    /**
     * @return string
     */
    protected function getTextKey()
    {
        return 'elements.task_delete_element_template';
    }
}
