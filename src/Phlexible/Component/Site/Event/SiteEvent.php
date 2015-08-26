<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
