<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Delegating tree mediator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingTreeMediator implements MediatorInterface
{
    /**
     * @var MediatorInterface[]
     */
    private $mediators = array();

    /**
     * @param MediatorInterface[] $mediators
     */
    public function __construct(array $mediators = array())
    {
        foreach ($mediators as $mediator) {
            $this->addMediator($mediator);
        }
    }

    /**
     * @param MediatorInterface $mediator
     *
     * @return $this
     */
    public function addMediator(MediatorInterface $mediator)
    {
        $this->mediators[] = $mediator;

        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function accept(NodeContext $node)
    {
        return $this->findMediator($node) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getField(NodeContext $node, $field, $language)
    {
        $mediator = $this->findMediator($node);

        if ($mediator instanceof ContentProviderInterface) {
            return $mediator->getField($node, $field, $language);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldMappings(NodeContext $node)
    {
        $mediator = $this->findMediator($node);

        if ($mediator instanceof ContentProviderInterface) {
            return $mediator->getFieldMappings($node);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(NodeContext $node, $language, $version = null)
    {
        $mediator = $this->findMediator($node);

        if ($mediator instanceof ContentProviderInterface) {
            return $mediator->getContent($node, $language, $version);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentVersions(NodeContext $node)
    {
        $mediator = $this->findMediator($node);

        if ($mediator instanceof ContentProviderInterface) {
            return $mediator->getContentVersions($node);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(NodeContext $node)
    {
        $mediator = $this->findMediator($node);

        if ($mediator instanceof ContentProviderInterface) {
            return $mediator->getTemplate($node);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function createNodeForContentDocument($contentDocument)
    {
        foreach ($this->mediators as $mediator) {
            $node = $mediator->createNodeForContentDocument($contentDocument);
            if ($node) {
                return $node;
            }
        }

        return null;
    }

    /**
     * @param NodeContext $node
     *
     * @return MediatorInterface|null
     */
    private function findMediator(NodeContext $node)
    {
        foreach ($this->mediators as $mediator) {
            if ($mediator->accept($node)) {
                return $mediator;
            }
        }

        return null;
    }
}
