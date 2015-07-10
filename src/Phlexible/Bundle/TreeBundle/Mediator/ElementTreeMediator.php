<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\TreeBundle\Entity\FullElementNode;
use Phlexible\Bundle\TreeBundle\Entity\StructureElementNode;
use Phlexible\Bundle\TreeBundle\Mediator\VersionStrategy\VersionStrategyInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Element tree mediator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementTreeMediator implements TreeMediatorInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var VersionStrategyInterface
     */
    private $versionStrategy;

    /**
     * @param ElementService           $elementService
     * @param VersionStrategyInterface $versionStrategy
     */
    public function __construct(ElementService $elementService, VersionStrategyInterface $versionStrategy)
    {
        $this->elementService = $elementService;
        $this->versionStrategy = $versionStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(NodeContext $node)
    {
        return $node->getNode()->getType() === 'element-full' || $node->getNode()->getType() === 'element-structure';
    }

    /**
     * {@inheritdoc}
     */
    public function getField(NodeContext $node, $field, $language)
    {
        $elementVersion = $this->getContentDocument($node, $language);

        return $elementVersion->getMappedField($field, $language);
    }

    /**
     * {@inheritdoc}
     *
     * @return ElementVersion
     */
    public function getContentDocument(NodeContext $node, $language = null)
    {
        return $this->versionStrategy->find($node, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(NodeContext $node)
    {
        $element = $this->elementService->findElement($node->getNode()->getTypeId());
        $elementSource = $this->elementService->findElementSource($element->getElementtypeId());

        $template = $elementSource->getTemplate();

        if ($template) {
            return $template;
        }

        return '::' . $elementSource->getName() . '.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(NodeContext $node)
    {
        return $node->getNode()->getType() === 'element-full';
    }

    /**
     * {@inheritdoc}
     */
    public function createNodeForContentDocument($contentDocument)
    {
        if (!$contentDocument instanceof ElementVersion) {
            return null;
        }

        switch ($contentDocument->getElementSource()->getType()) {
            case 'full':
                $node = new FullElementNode();
                break;

            case 'structure':
                $node = new StructureElementNode();
                break;

            default:
                throw new \InvalidArgumentException("Can't create node for element type {$contentDocument->getElementSource()->getType()}.");
        }

        $node
            ->setType('element')
            ->setTypeId($contentDocument->getElement()->getEid());

        return $node;
    }
}
