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
     * @var SiteHostnameMapper
     */
    private $hostnameMapper;

    /**
     * @param SiteManagerInterface $siteManager
     * @param SiteHostnameMapper   $hostnameMapper
     */
    public function __construct(SiteManagerInterface $siteManager, SiteHostnameMapper $hostnameMapper)
    {
        $this->siteManager = $siteManager;
        $this->hostnameMapper = $hostnameMapper;
    }

    /**
     * @param Request $request
     *
     * @return Site
     */
    public function matchRequest(Request $request)
    {
        $defaultSiteroot = null;

        $hostname = $this->hostnameMapper->fromLocal($request->getHttpHost());

        foreach ($this->siteManager->findAll() as $siteroot) {
            $siterootHostname = $siteroot->getHostname();
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
