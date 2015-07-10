<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Mediator\VersionStrategy;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;

/**
 * Version strategy interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface VersionStrategyInterface
{
    /**
     * @param TeaserManagerInterface $teaserManager
     * @param Teaser                 $teaser
     * @param string                 $language
     *
     * @return ElementVersion
     */
    public function find(TeaserManagerInterface $teaserManager, Teaser $teaser, $language);
}
