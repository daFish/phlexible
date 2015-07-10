<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Event;

use Phlexible\Bundle\CmsBundle\Configurator\Configuration;
use Symfony\Component\EventDispatcher\Event;

/**
 * Configure event
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ConfigureEvent extends Event
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
