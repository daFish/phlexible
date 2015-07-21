<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Teaser;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Mediator\TeaserMediatorInterface;
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
     * @var TeaserMediatorInterface
     */
    private $mediator;

    /**
     * @var Teaser
     */
    private $teaser;

    /**
     * @var NodeContext
     */
    private $node;

    /**
     * @var string
     */
    private $language;

    /**
     * @param TeaserMediatorInterface $mediator
     * @param Teaser                  $teaser
     * @param NodeContext             $node
     * @param string                  $language
     */
    public function __construct(TeaserMediatorInterface $mediator, Teaser $teaser, NodeContext $node, $language)
    {
        $this->mediator = $mediator;
        $this->teaser = $teaser;
        $this->node = $node;
        $this->language = $language;
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
    public function getId()
    {
        return $this->teaser->getId();
    }

    /**
     * @return int
     */
    public function getNodeId()
    {
        return $this->teaser->getNodeId();
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->teaser->getType();
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->teaser->getTypeId();
    }

    /**
     * @return int
     */
    public function isHidden()
    {
        return $this->teaser->isHidden();
    }

    /**
     * @return int
     */
    public function isStopped()
    {
        return $this->teaser->isStopped();
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return array
     */
    public function getAttribute($key, $default = null)
    {
        return $this->teaser->getAttribute($key, $default);
    }

    /**
     * @param string $language
     *
     * @return \DateTime|null
     */
    public function getPublishedAt($language = null)
    {
        return $this->mediator->getPublishedAt($this->teaser, $language ?: $this->language);
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function isAvailable($language = null)
    {
        return $this->mediator->isPublished($this->teaser, $language ?: $this->language);
    }

    /**
     * @return bool
     */
    public function isViewable()
    {
        return $this->mediator->isViewable($this->teaser);
    }

    /**
     * @param string $field
     * @param string $language
     *
     * @return bool
     */
    public function getField($field, $language = null)
    {
        return $this->mediator->getField($this->teaser, $field, $language ?: $this->language);
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function getContent($language = null)
    {
        return $this->mediator->getContent($this->teaser, $language ?: $this->language);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->mediator->getTemplate($this->teaser);
    }
}
