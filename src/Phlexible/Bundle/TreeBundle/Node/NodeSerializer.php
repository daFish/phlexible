<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node;

use Phlexible\Bundle\TreeBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Model\PageInterface;
use Phlexible\Bundle\TreeBundle\Model\PartInterface;
use Phlexible\Bundle\TreeBundle\Model\StructureInterface;
use Phlexible\Component\AccessControl\Permission\PermissionRegistry;
use Phlexible\Component\Node\Model\NodeManagerInterface;
use Phlexible\Component\NodeType\Model\NodeTypeManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Tree interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeSerializer
{
    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var NodeTypeManagerInterface
     */
    private $nodeTypeManager;

    /**
     * @var PermissionRegistry
     */
    private $permissionRegistry;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param IconResolver                  $iconResolver
     * @param NodeManagerInterface          $nodeManager
     * @param NodeTypeManagerInterface      $nodeTypeManager
     * @param PermissionRegistry            $permissionRegistry
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        IconResolver $iconResolver,
        NodeManagerInterface $nodeManager,
        NodeTypeManagerInterface $nodeTypeManager,
        PermissionRegistry $permissionRegistry,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->iconResolver = $iconResolver;
        $this->nodeManager = $nodeManager;
        $this->nodeTypeManager = $nodeTypeManager;
        $this->permissionRegistry = $permissionRegistry;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Serialize nodes.
     *
     * @param NodeContext[] $nodes
     * @param string        $language
     *
     * @return array
     */
    public function serializeNodes(array $nodes, $language)
    {
        $return = array();

        foreach ($nodes as $node) {
            $nodeData = $this->serializeNode($node, $language);

            if ($nodeData) {
                $return[] = $nodeData;
            }
        }

        return $return;
    }

    /**
     * Serialize node.
     *
     * @param NodeContext $node
     * @param string      $language
     *
     * @return array
     */
    public function serializeNode(NodeContext $node, $language)
    {
        $permissions = array();
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            if (!$this->authorizationChecker->isGranted(array('permission' => 'VIEW', 'language' => $language), $node)) {
                return null;
            }

            // TODO: fix
            foreach ($this->permissionRegistry->get(get_class($node))->all() as $permission) {
                $permissions[] = $permission->getName();
            }
        } else {
            foreach ($this->permissionRegistry->get(get_class($node))->all() as $permission) {
                $permissions[] = $permission->getName();
            }
        }

        // TODO: both from mediator?
        $allowedTypes = array_keys($this->nodeTypeManager->getTypesForNode($node));
        $hideChildren = false;

        $qtip = "ID: {$node->getId()}<br />Type: {$node->getContentType()}<br />Type ID: {$node->getContentId()}";

        $type = 'unknown';
        if ($node->getNode() instanceof PageInterface) {
            $type = 'page';
        } elseif ($node->getNode() instanceof StructureInterface) {
            $type = 'structure';
        } elseif ($node->getNode() instanceof PartInterface) {
            $type = 'part';
        }

        $data = array(
            'id' => $node->getId(),
            'text' => $node->getTitle(),
            'allow_drag' => true,
            'areas' => array(),
            'qtip' => $qtip,

            'workspace' => $node->getWorkspace(),
            'locale' => $node->getLocale(),
            'siterootId' => $node->getSiterootId(),
            'path' => $node->getPath(),
            'parentPath' => $node->getParentPath(),
            'type' => $type,
            'contentType' => $node->getContentType(),
            'contentId' => $node->getContentId(),
            'contentVersion' => $node->getContentVersion(),
            'title' => $node->getTitle(),
            'pageTitle' => $node->getTitle(), // @deprecated
            'navigationTitle' => $node->getNavigationTitle(),
            'backendTitle' => $node->getBackendTitle(),
            'slug' => $node->getSlug(),
            'customDate' => $node->getField('date', $language),
            'forward' => $node->getField('forward', $language),
            'sortMode' => $node->getSortMode(),
            'sortDir' => $node->getSortDir(),
            'sort' => $node->getSort(),
            //'icon'             => $this->iconResolver->resolveNode($node),
            'iconCls' => 'p-icon-document-text-image',
            'inNavigation' => $node->getInNavigation(),
            'isRestricted' => $node->getAttribute('security'),
            'isInstance' => $node->getTree()->isInstance($node),
            'createdAt' => $node->getCreatedAt()->format('Y-m-d H:i:s'),
            'createdBy' => $node->getCreateUserId(),
            'modifiedAt' => $node->getModifiedAt() ? $node->getModifiedAt()->format('Y-m-d H:i:s') : null,
            'modifiedBy' => $node->getModifyUserId(),
            'publishedAt' => $node->getPublishedAt() ? $node->getPublishedAt()->format('Y-m-d H:i:s') : null,
            'publishedBy' => $node->getPublishUserId(),
            'isPublished' => $node->getWorkspace() === 'live',
            'isAsync' => $node->isAsync(),
            'publishedVersion' => $node->getContentVersion(),

            'allowedTypes' => $allowedTypes,
            'permissions' => $permissions,
            'hideChildren' => $hideChildren,
        );

        if (count($node->getTree()->getChildren($node)) && !$hideChildren) {
            $data['leaf'] = false;
            $data['expanded'] = false;
        } else {
            $data['leaf'] = true;
            $data['expanded'] = false;
        }

        if ($node->getNode()->isRoot()) {
            $data['cls'] = 'siteroot-node';
            $data['expanded'] = true;
        }

        return $data;
    }
}
