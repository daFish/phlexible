<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Element\Publish;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeInterface;
use Phlexible\Component\Elementtype\ElementtypeService;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Selector
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Selector
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param ElementService                $elementService
     * @param ElementtypeService            $elementtypeService
     * @param TreeManagerInterface          $treeManager
     * @param TeaserManagerInterface        $teaserManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        ElementService $elementService,
        ElementtypeService $elementtypeService,
        TreeManagerInterface $treeManager,
        TeaserManagerInterface $teaserManager,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->elementService = $elementService;
        $this->elementtypeService = $elementtypeService;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param int    $treeId
     * @param string $language
     * @param int    $version
     * @param bool   $includeElements
     * @param bool   $includeElementInstances
     * @param bool   $includeTeasers
     * @param bool   $includeTeaserInstances
     * @param bool   $recursive
     * @param bool   $onlyOffline
     * @param bool   $onlyAsync
     *
     * @return array
     */
    public function select(
        $treeId,
        $language,
        $version,
        $includeElements,
        $includeElementInstances,
        $includeTeasers,
        $includeTeaserInstances,
        $recursive,
        $onlyOffline,
        $onlyAsync)
    {
        $tree = $this->treeManager->getByNodeId($treeId);
        $node = $tree->get($treeId);

        $selection = new Selection();

        if ($includeElements) {
            $this->handleNode(
                $selection,
                0,
                implode('/', $tree->getIdPath($node)),
                $node,
                $version,
                $language,
                $onlyAsync,
                $onlyOffline
            );
        }
        if ($includeTeasers) {
            $this->handleTeasers(
                $selection,
                0,
                implode('/', $tree->getIdPath($node)),
                $node,
                $language,
                $onlyAsync,
                $onlyOffline,
                $includeTeaserInstances
            );
        }

        if ($recursive) {
            $rii = new \RecursiveIteratorIterator($node->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($rii as $childNode) {
                /* @var $childNode NodeInterface */

                set_time_limit(5);

                if ($includeElements) {
                    $this->handleNode(
                        $selection,
                        $rii->getDepth() + 1,
                        implode('/', $tree->getIdPath($childNode)),
                        $childNode,
                        null,
                        $language,
                        $onlyAsync,
                        $onlyOffline
                    );
                }
                if ($includeTeasers) {
                    $this->handleTeasers(
                        $selection,
                        $rii->getDepth() + 1,
                        implode('/', $tree->getIdPath($childNode)),
                        $childNode,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        $includeTeaserInstances
                    );
                }
            }
        }

        foreach ($selection->all() as $selectionItem) {
            if ($includeElementInstances && $selectionItem->getTarget() instanceof NodeInterface) {
                $instanceNodes = $this->treeManager->getInstanceNodes($selectionItem->getTarget());

                foreach ($instanceNodes as $instanceNode) {
                    /* @var $instanceNode NodeInterface */

                    $this->handleNode(
                        $selection,
                        $selectionItem->getDepth(),
                        $selectionItem->getDepth(),
                        $instanceNode,
                        null,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        true
                    );
                }
            } elseif ($includeTeaserInstances && $selectionItem->getTarget() instanceof Teaser) {
                $instanceTeasers = $this->teaserManager->getInstances($selectionItem->getTarget());

                foreach ($instanceTeasers as $instanceTeaser) {
                    $this->handleTeaser(
                        $selection,
                        $selectionItem->getDepth(),
                        $instanceTeaser,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        true
                    );
                }
            }
        }

        return $selection;
    }

    /**
     * @param Selection         $selection
     * @param int               $depth
     * @param array             $path
     * @param NodeInterface $node
     * @param int               $version
     * @param string            $language
     * @param bool              $onlyAsync
     * @param bool              $onlyOffline
     * @param bool              $isInstance
     */
    private function handleNode(
        Selection $selection,
        $depth,
        $path,
        NodeInterface $node,
        $version,
        $language,
        $onlyAsync,
        $onlyOffline,
        $isInstance = false)
    {
        if ($selection->has($node, $language)) {
            return;
        }

        $include = true;

        if ($onlyAsync || $onlyOffline) {
            $include = false;

            if ($onlyAsync && $node->getTree()->isAsync($node, $language)) {
                $include = true;
            }
            if ($onlyOffline && !$node->getTree()->isPublished($node, $language)) {
                $include = true;
            }
        }
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            if (!$this->authorizationChecker->isGranted($node, array('right' => 'PUBLISH', 'language' => $language))) {
                $include = false;
            }
        }

        if (!$include) {
            return;
        }

        $element = $this->elementService->findElement($node->getTypeId());
        if ($version) {
            $elementVersion = $this->elementService->findElementVersion($element, $version);
        } else {
            $elementVersion = $this->elementService->findLatestElementVersion($element);
        }

        $selection->add(
            new SelectionItem(
                $node,
                $elementVersion->getVersion(),
                $language,
                $elementVersion->getBackendTitle($language),
                $isInstance,
                $depth,
                $path . '+' . $language . '+' . $node->getId() . '+' . $language
            )
        );
    }

    /**
     * @param Selection         $selection
     * @param int               $depth
     * @param array             $path
     * @param NodeInterface $node
     * @param string            $language
     * @param bool              $onlyAsync
     * @param bool              $onlyOffline
     * @param bool              $includeTeaserInstances
     */
    private function handleTeasers(
        Selection $selection,
        $depth,
        $path,
        NodeInterface $node,
        $language,
        $onlyAsync,
        $onlyOffline,
        $includeTeaserInstances)
    {
        $element = $this->elementService->findElement($node->getTypeId());
        $elementtype = $this->elementService->findElementtype($element);

        $layoutareas = array();
        // TODO: repair
        foreach ($this->elementService->findElementtypeByType('layout') as $layoutarea) {
            if (in_array($elementtype, $this->elementService->findAllowedParents($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        foreach ($layoutareas as $layoutarea) {
            $teasers = $this->teaserManager->findForLayoutAreaAndNodeContext($layoutarea, $node);

            foreach ($teasers as $teaser) {
                $this->handleTeaser($selection, $depth + 1, $path, $teaser, $language, $onlyAsync, $onlyOffline);
            }
        }
    }

    /**
     * @param Selection $selection
     * @param int       $depth
     * @param array     $path
     * @param Teaser    $teaser
     * @param string    $language
     * @param bool      $onlyAsync
     * @param bool      $onlyOffline
     * @param bool      $isInstance
     */
    private function handleTeaser(
        Selection $selection,
        $depth,
        $path,
        Teaser $teaser,
        $language,
        $onlyAsync,
        $onlyOffline,
        $isInstance = false)
    {
        if ($teaser->getType() !== 'element') {
            return;
        }

        if ($selection->has($teaser, $language)) {
            return;
        }

        $isAsync = $this->teaserManager->isAsync($teaser, $language);
        $isPublished = $this->teaserManager->isPublished($teaser, $language);

        $include = true;
        if ($onlyAsync || $onlyOffline) {
            $include = false;

            if ($onlyAsync && $isAsync) {
                $include = true;
            }
            if ($onlyOffline && !$isPublished) {
                $include = true;
            }
        }

        if (!$include) {
            return;
        }

        $element = $this->elementService->findElement($teaser->getTypeId());
        $elementVersion = $this->elementService->findLatestElementVersion($element);

        $selection->add(
            new SelectionItem(
                $teaser,
                $elementVersion->getVersion(),
                $language,
                $elementVersion->getBackendTitle($language),
                $isInstance,
                $depth,
                $path . '+' . $language . '+' . $teaser->getId() . '+' . $language
            )
        );
    }
}
