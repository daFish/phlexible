<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Teaser;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Teaser context
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserContext
{
    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var Teaser
     */
    private $teaser;

    /**
     * @var NodeContext
     */
    private $node;

    /**
     * @param TeaserManagerInterface $teaserManager
     * @param Teaser                 $teaser
     * @param NodeContext            $node
     */
    public function __construct(TeaserManagerInterface $teaserManager, Teaser $teaser, NodeContext $node)
    {
        $this->teaserManager = $teaserManager;
        $this->teaser = $teaser;
        $this->node = $node;
    }

    /**
     * @return Teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @return NodeContext
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->teaser->getId();
    }

    /**
     * @return bool
     */
    public function title()
    {
        return $this->field("page");
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return array
     */
    public function attribute($key, $default = null)
    {
        return $this->teaser->getAttribute($key, $default);
    }

    /**
     * @param string $language
     *
     * @return \DateTime|null
     */
    public function publishedAt($language = null)
    {
        return $this->teaserManager->getPublishedAt($this->teaser, $language);
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function available($language = null)
    {
        return $this->teaserManager->isPublished($this->teaser, $language);
    }

    /**
     * @return bool
     */
    public function viewable()
    {
        return $this->teaserManager->isViewable($this->teaser);
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function content($language = null)
    {
        return $this->teaserManager->getContent($this->teaser, $language);
    }

    /**
     * @param string $field
     * @param string $language
     *
     * @return bool
     */
    public function field($field, $language = null)
    {
        return $this->teaserManager->getField($this->teaser, $field, $language);
    }
}
