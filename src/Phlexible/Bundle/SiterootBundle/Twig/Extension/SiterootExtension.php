<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Twig\Extension;

use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Phlexible\Component\Site\Site\SiteRequestMatcher;
use Phlexible\Component\Site\Site\SitesAccessor;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig siteroot extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootExtension extends \Twig_Extension
{
    /**
     * @var SiteManagerInterface
     */
    private $siterootManager;

    /**
     * @var SiteRequestMatcher
     */
    private $siterootRequestMatcher;

    /**
     * @var \Phlexible\Component\Site\Site\SitesAccessor
     */
    private $siterootsAccessor;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param SiteManagerInterface $siterootManager
     * @param SiteRequestMatcher   $siterootRequestMatcher
     * @param \Phlexible\Component\Site\Site\SitesAccessor        $siterootsAccessor
     * @param RequestStack             $requestStack
     */
    public function __construct(
        SiteManagerInterface $siterootManager,
        SiteRequestMatcher $siterootRequestMatcher,
        SitesAccessor $siterootsAccessor,
        RequestStack $requestStack
    )
    {
        $this->siterootManager = $siterootManager;
        $this->siterootRequestMatcher = $siterootRequestMatcher;
        $this->siterootsAccessor = $siterootsAccessor;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('special_tid', array($this, 'specialTid')),
            new \Twig_SimpleFunction('current_siteroot', array($this, 'currentSiteroot')),
        );
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'siteroots' => $this->siterootsAccessor
        );
    }

    /**
     * @return \Phlexible\Component\Site\Domain\Site
     */
    public function currentSiteroot()
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->attributes->has('siteroot')) {
            $siteroot = $request->attributes->get('siteroot');
        } else {
            $siteroot = $this->siterootRequestMatcher->matchRequest($request);
        }

        return $siteroot;
    }

    /**
     * @param string $name
     * @param string $language
     *
     * @return int|null
     */
    public function specialTid($name, $language = null)
    {
        $siteroot = $this->currentSiteroot();

        if (!$language) {
            $language = $this->requestStack->getCurrentRequest()->getLocale();
        }

        return $siteroot->getSpecialTid($language, $name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_siteroot';
    }
}
