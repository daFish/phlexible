<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\Site;

use Phlexible\Component\Site\Domain\Site;

/**
 * Site hostname generator.
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
