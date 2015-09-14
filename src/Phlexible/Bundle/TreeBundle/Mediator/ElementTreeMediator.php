<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Doctrine\ORM\EntityManagerInterface;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Proxy\ClassManager;
use Phlexible\Bundle\TreeBundle\Entity\PageNode;
use Phlexible\Bundle\TreeBundle\Entity\PartNode;
use Phlexible\Bundle\TreeBundle\Entity\StructureNode;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Elementtype\Domain\Elementtype;

/**
 * Element tree mediator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementTreeMediator implements ContentProviderInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ClassManager
     */
    private $classManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param ElementService         $elementService
     * @param ClassManager           $classManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ElementService $elementService,
        ClassManager $classManager,
        EntityManagerInterface $entityManager
    ) {
        $this->elementService = $elementService;
        $this->classManager = $classManager;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(NodeContext $node)
    {
        return $this->classManager->containsName($node->getNode()->getContentType());
    }

    /**
     * {@inheritdoc}
     */
    public function getField(NodeContext $node, $field, $language)
    {
        $element = $this->elementService->findElement($node->getContentId());
        $elementVersion = $this->elementService->findElementVersion($element, $node->getContentVersion());

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
        $element = $this->elementService->findElement($node->getContentId());
        $elementVersion = $this->elementService->findElementVersion($element, $node->getContentVersion());

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

        return null;
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
