<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Doctrine\ORM\EntityManagerInterface;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Proxy\ClassManager;
use Phlexible\Bundle\TreeBundle\Entity\PageNode;
use Phlexible\Bundle\TreeBundle\Entity\PartNode;
use Phlexible\Bundle\TreeBundle\Entity\StructureNode;
use Phlexible\Bundle\TreeBundle\Mediator\VersionStrategy\VersionStrategyInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Elementtype\Domain\Elementtype;

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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param ElementService           $elementService
     * @param VersionStrategyInterface $versionStrategy
     * @param ClassManager             $classManager
     * @param EntityManagerInterface   $entityManager
     */
    public function __construct(
        ElementService $elementService,
        VersionStrategyInterface $versionStrategy,
        ClassManager $classManager,
        EntityManagerInterface $entityManager
    ) {
        $this->elementService = $elementService;
        $this->versionStrategy = $versionStrategy;
        $this->classManager = $classManager;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(NodeContext $node)
    {
        return $node->getNode()->getContentType() === 'element' || $node->getNode()->getContentType() === 'element';
    }

    /**
     * @param VersionStrategyInterface $versionStrategy
     */
    public function setVersionStrategy(VersionStrategyInterface $versionStrategy)
    {
        $this->versionStrategy = $versionStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function getField(NodeContext $node, $field, $language)
    {
        $elementVersion = $this->versionStrategy->findElementVersion($node, $language);

        if (!$elementVersion) {
            return null;
        }

        $repo = $this->entityManager->getRepository('PhlexibleTreeBundle:NodeMappedField');
        $mappedField = $repo->findOneBy(array('nodeId' => $node->getId(), 'language' => $language, 'version' => $elementVersion->getVersion()));

        if (!$mappedField) {
            return null;
        }

        $method = 'get' . ucfirst($field);

        return $mappedField->$method();
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldMappings(NodeContext $node)
    {
        $element = $this->elementService->findElement($node->getContentId());

        if (!$element) {
            return null;
        }

        $elementtype = $this->elementService->findElementtype($element);

        return $elementtype->getMappings();
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(NodeContext $node, $language, $version = null)
    {
        if ($version) {
            $element = $this->elementService->findElement($node->getContentId());
            $elementVersion = $this->elementService->findElementVersion($element, $version);
        } else {
            $elementVersion = $this->versionStrategy->findElementVersion($node, $language);
        }

        if (!$elementVersion) {
            return null;
        }

        return $this->classManager->create($elementVersion);
    }

    /**
     * {@inheritdoc}
     */
    public function getContentVersions(NodeContext $node)
    {
        $element = $this->elementService->findElement($node->getContentId());

        if (!$element) {
            return null;
        }

        return array_column($this->elementService->getVersions($element), 'version');
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
            case Elementtype::TYPE_FULL:
                $node = new PageNode();
                break;

            case Elementtype::TYPE_STRUCTURE:
                $node = new StructureNode();
                break;

            case Elementtype::TYPE_PART:
                $node = new PartNode();
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
