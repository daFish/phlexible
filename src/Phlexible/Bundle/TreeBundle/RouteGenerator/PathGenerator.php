<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\RouteGenerator;

use Cocur\Slugify\Slugify;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Path generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PathGenerator implements PathGeneratorInterface
{
    /**
     * @var Slugify
     */
    private $slugify;

    /**
     * @var PathDecoratorInterface[]
     */
    private $decorators = array();

    /**
     * @param PathDecoratorInterface[] $decorators
     */
    public function __construct(array $decorators = array())
    {
        $this->slugify = new Slugify();

        foreach ($decorators as $decorator) {
            $this->addDecorator($decorator);
        }
    }

    /**
     * @param PathDecoratorInterface $decorator
     *
     * @return $this
     */
    public function addDecorator(PathDecoratorInterface $decorator)
    {
        $this->decorators[] = $decorator;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePath(NodeContext $node, $language)
    {
        $tree = $node->getTree();

        // we reverse the order to determine if this leaf is no full element
        // if the is the case we don't have to continue, only full elements
        // have paths
        $pathNodes = array_reverse($tree->getPath($node));

        $parts = array();

        foreach ($pathNodes as $pathNode) {
            /* @var $pathNode NodeContext */
            if ($pathNode->isViewable($language)) {
                $parts[] = $node->getField('navigation', $language);
            }
        }

        if (!count($parts)) {
            if (!count($pathNodes)) {
                return '';
            }

            $currentNode = $pathNodes[0];
            $parts[] = $currentNode->getField('navigation', $language);
        }

        $parts = array_map(array($this->slugify, 'slugify'), $parts);

        $path = new Path(array('/' . implode('/', array_reverse($parts))));

        foreach ($this->decorators as $decorator) {
            $decorator->decoratePath($path, $node, $language);
        }

        return (string) $path;
    }
}
