<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\Model;

use Phlexible\Component\Site\Domain\Site;

/**
 * Siteroot manager interface
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
interface SiteManagerInterface
{
    /**
     * @param string $id
     *
     * @return Site|null
     */
    public function find($id);

    /**
     * @return Site[]
     */
    public function findAll();

    /**
     * @param Site $siteroot
     */
    public function updateSite(Site $siteroot);

    /**
     * @param Site $siteroot
     */
    public function deleteSite(Site $siteroot);
}
