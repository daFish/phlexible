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
use Phlexible\Bundle\ElementBundle\Proxy\ClassManager;
use Phlexible\Bundle\TreeBundle\Entity\PageNode;
use Phlexible\Bundle\TreeBundle\Entity\StructureNode;
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
     * @var ClassManager
     */
    private $classManager;

    /**
     * @param ElementService           $elementService
     * @param VersionStrategyInterface $versionStrategy
     * @param ClassManager             $classManager
     */
    public function __construct(ElementService $elementService, VersionStrategyInterface $versionStrategy, ClassManager $classManager)
    {
        $this->elementService = $elementService;
        $this->versionStrategy = $versionStrategy;
        $this->classManager = $classManager;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(NodeContext $node)
    {
        return $node->getNode()->getContentType() === 'element' || $node->getNode()->getContentType() === 'element';
    }

    /**
     * {@inheritdoc}
     */
    public function getField(NodeContext $node, $field, $language)
    {
        $elementVersion = $this->versionStrategy->find($node, $language);

        if (!$elementVersion) {
            return null;
        }

        return $elementVersion->getMappedField($field, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getContentDocument(NodeContext $node, $language = null)
    {
        $elementVersion = $this->versionStrategy->find($node, $language);

        if (!$elementVersion) {
            return null;
        }

        return $this->classManager->create($elementVersion);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(NodeContext $node)
    {
        $element = $this->elementService->findElement($node->getNode()->getContentId());
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
        return $node->getNode()->getContentType() === 'element';
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
                $node = new PageNode();
                break;

            case 'structure':
                $node = new StructureNode();
                break;

            default:
                throw new \InvalidArgumentException("Can't create node for element type {$contentDocument->getElementSource()->getType()}.");
        }

        $node
            ->setContentType('element')
            ->setContentId($contentDocument->getElement()->getEid());

        return $node;
    }
}
