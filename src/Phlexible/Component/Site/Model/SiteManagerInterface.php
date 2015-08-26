<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
     * @return null|Site
     */
    public function find($id);

    /**
     * @return \Phlexible\Component\Site\Domain\Site[]
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
