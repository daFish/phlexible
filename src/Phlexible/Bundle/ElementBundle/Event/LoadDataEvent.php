<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @var string
     */
    private $language;

    /**
     * @var object
     */
    private $data;

    /**
     * @param NodeContext $node
     * @param string      $language
     * @param object      $data
     */
    public function __construct(NodeContext $node, $language, $data)
    {
        $this->node = $node;
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
