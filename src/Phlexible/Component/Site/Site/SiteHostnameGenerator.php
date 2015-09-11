<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\Site;

use Phlexible\Component\Site\Domain\Site;

/**
 * Site hostname generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteHostnameGenerator
{
    /**
     * @var SiteHostnameMapper
     */
    private $mapper;

    /**
     * @param SiteHostnameMapper $mapper
     */
    public function __construct(SiteHostnameMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param Site $site
     *
     * @return string
     */
    public function generate(Site $site)
    {
        $hostname = $site->getHostname();

        return $this->mapper->toLocal($hostname);
    }
}
