<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Symfony\Component\EventDispatcher\Event;

/**
 * Load data event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LoadDataEvent extends Event
{
    /**
     * @var NodeContext
     */
    private $node;

    /**
     * @var Teaser
     */
    private $teaser;

    /**
     * @var string
     */
    private $language;

    /**
     * @var object
     */
    private $data;

    /**
     * @param NodeContext $node
     * @param Teaser      $teaser
     * @param string      $language
     * @param object      $data
     */
    public function __construct(NodeContext $node, Teaser $teaser = null, $language, $data)
    {
        $this->node = $node;
        $this->teaser = $teaser;
        $this->language = $language;
        $this->data = $data;
    }

    /**
     * @return NodeContext
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return Teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }
}
