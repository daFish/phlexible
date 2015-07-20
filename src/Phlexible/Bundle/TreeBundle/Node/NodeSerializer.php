<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Model\NodeManagerInterface;
use Phlexible\Component\AccessControl\Permission\PermissionRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeSerializer
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var PermissionRegistry
     */
    private $permissionRegistry;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param ElementService                $elementService
     * @param IconResolver                  $iconResolver
     * @param NodeManagerInterface          $nodeManager
     * @param PermissionRegistry            $permissionRegistry
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        ElementService $elementService,
        IconResolver $iconResolver,
        NodeManagerInterface $nodeManager,
        PermissionRegistry $permissionRegistry,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->elementService = $elementService;
        $this->iconResolver = $iconResolver;
        $this->nodeManager = $nodeManager;
        $this->permissionRegistry = $permissionRegistry;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Serialize nodes
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
     * Serialize node
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

        $typeId = $node->getTypeId();
        $element = $this->elementService->findElement($typeId);

        $elementtype = $this->elementService->findElementtype($element);
        $allowedElementtypeIds = array();
        foreach ($this->elementService->findAllowedChildren($elementtype) as $allowedElementtype) {
            $allowedElementtypeIds[] = $allowedElementtype->getId();
        }

        $qtip = "ID: {$node->getId()}<br />Type: {$node->getType()}<br />Type ID: {$node->getTypeId()}";

        $data = array(
            'id'              => $node->getId(),
            'text'            => $node->getField('backend', $language),

            'siterootId'      => $node->getSiterootId(),
            'type'            => $node->getType(),
            'typeId'          => $node->getTypeId(),
            'backendTitle'    => $node->getField('backend', $language),
            'pageTitle'       => $node->getField('page', $language),
            'navigationTitle' => $node->getField('navigation', $language),
            'customDate'      => $node->getField('date', $language),
            'forward'         => $node->getField('redirect', $language),
            'sortMode'        => $node->getSortMode(),
            'sortDir'         => $node->getSortDir(),
            'sort'            => $node->getSort(),
            'icon'            => $this->iconResolver->resolveNode($node, $language),
            'inNavigation'    => $node->getInNavigation(),
            'isRestricted'    => $node->getAttribute('security'),
            'isInstance'      => $node->getTree()->isInstance($node),
            'createdAt'       => $node->getCreatedAt()->format('Y-m-d H:i:s'),
            'createdBy'       => $node->getCreateUserId(),
            'isPublished'      => $node->isPublished($language),
            'publishedAt'      => $node->getPublishedAt($language) ? $node->getPublishedAt($language)->format('Y-m-d H:i:s') : null,
            'publishedBy'      => $node->getPublishUserId($language),
            'isAsync'          => $node->isAsync($language),
            'publishedVersion' => $node->getPublishedVersion($language),

            'allowedChildren' => $allowedElementtypeIds,
            'permissions'     => $permissions,
            'hideChildren'    => $elementtype->getHideChildren() ? true : false,

            'elementtypeId'     => $elementtype->getId(),
            'elementtypeName'   => $elementtype->getTitle(),
            'elementtypeType'   => $elementtype->getType(),

            'allow_drag'          => true,
            'areas'               => array(),
            'qtip'                => $qtip,
        );

        if (count($node->getTree()->getChildren($node)) && !$elementtype->getHideChildren()) {
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
