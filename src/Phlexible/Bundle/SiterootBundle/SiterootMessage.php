<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Siteroot message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultChannel()
    {
        return 'siteroots';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultRole()
    {
        return 'ROLE_SITEROOT';
    }
}
