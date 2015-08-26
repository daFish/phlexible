<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\Site;

use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Siteroot request matcher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteRequestMatcher
{
    /**
     * @var SiteManagerInterface
     */
    private $siteManager;

    /**
     * @var array
     */
    private $urlMappings;

    /**
     * @param SiteManagerInterface $siteManager
     * @param array                $urlMappings
     */
    public function __construct(SiteManagerInterface $siteManager, array $urlMappings)
    {
        $this->siteManager = $siteManager;
        $this->urlMappings = $urlMappings;
    }

    /**
     * @param Request $request
     *
     * @return Site
     */
    public function matchRequest(Request $request)
    {
        $defaultSiteroot = null;

        $hostname = $request->getHttpHost();

        foreach ($this->siteManager->findAll() as $siteroot) {
            $siterootHostname = $siteroot->getHostname();
            if (isset($this->urlMappings[$siterootHostname])) {
                $siterootHostname = $this->urlMappings[$siterootHostname];
            }
            if ($siterootHostname === $hostname) {
                return $siteroot;
            }
            if ($siteroot->isDefault()) {
                $defaultSiteroot = $siteroot;
            }
        }

        if ($defaultSiteroot) {
            return $defaultSiteroot;
        }

        return null;
    }
}
