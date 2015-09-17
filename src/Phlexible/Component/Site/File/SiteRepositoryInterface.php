<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\File;

use Phlexible\Component\Site\Domain\Site;

/**
 * Site repository interface.
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
