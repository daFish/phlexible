<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\File;

use Phlexible\Component\Site\Domain\Site;

/**
 * Site repository interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SiteRepositoryInterface
{
    /**
     * @return Site[]
     */
    public function loadAll();

    /**
     * @param string $siteId
     *
     * @return Site
     */
    public function load($siteId);

    /**
     * @param Site $site
     */
    public function write(Site $site);
}