<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\Event;

use Phlexible\Component\Site\Domain\Site;
use Symfony\Component\EventDispatcher\Event;

/**
 * Site event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteEvent extends Event
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }
}
