<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\Site;

/**
 * Site hostname mapper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteHostnameMapper
{
    /**
     * @var array
     */
    private $urlMappings;

    /**
     * @param array $urlMappings
     */
    public function __construct(array $urlMappings)
    {
        $this->urlMappings = $urlMappings;
    }

    /**
     * @param string $hostname
     *
     * @return string
     */
    public function toLocal($hostname)
    {
        if (isset($this->urlMappings[$hostname])) {
            return $this->urlMappings[$hostname];
        }

        return $hostname;
    }

    /**
     * @param string $localHostname
     *
     * @return string
     */
    public function fromLocal($localHostname)
    {
        $hostname = array_search($localHostname, $this->urlMappings);
        if ($hostname !== false) {
            return $hostname;
        }

        return $localHostname;
    }
}
