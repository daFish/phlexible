<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Mediator;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;

/**
 * Teaser mediator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TeaserMediatorInterface
{
    /**
     * @param Teaser $teaser
     *
     * @return bool
     */
    public function accept(Teaser $teaser);

    /**
     * @param TeaserManagerInterface $teaserManager
     * @param Teaser                 $teaser
     * @param string                 $field
     * @param string                 $language
     *
     * @return string
     */
    public function getField(TeaserManagerInterface $teaserManager, Teaser $teaser, $field, $language);

    /**
     * @param TeaserManagerInterface $teaserManager
     * @param Teaser                 $teaser
     * @param string                 $language
     *
     * @return mixed
     */
    public function getContentDocument(TeaserManagerInterface $teaserManager, Teaser $teaser, $language);

    /**
     * @param TeaserManagerInterface $teaserManager
     * @param Teaser                 $teaser
     *
     * @return string
     */
    public function getTemplate(TeaserManagerInterface $teaserManager, Teaser $teaser);
}
