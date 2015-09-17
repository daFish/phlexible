<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Event;

use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;
use Symfony\Component\EventDispatcher\Event;

/**
 * Access control entry event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlEntryEvent extends Event
{
    /**
     * @var AccessControlEntry
     */
    private $accessControlEntry;

    /**
     * @param AccessControlEntry $accessControlEntry
     */
    public function __construct(AccessControlEntry $accessControlEntry)
    {
        $this->accessControlEntry = $accessControlEntry;
    }

    /**
     * @return AccessControlEntry
     */
    public function getAccessControlEntry()
    {
        return $this->accessControlEntry;
    }
}
