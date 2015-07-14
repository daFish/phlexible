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
        $userRights = array();
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            if (!$this->authorizationChecker->isGranted(array('permission' => 'VIEW', 'language' => $language), $node)) {
                return null;
            }

            // TODO: fix
            $userRights = array();
            foreach ($this->permissionRegistry->get(get_class($node))->all() as $permission) {
                $userRights[] = $permission->getName();
            }
        } else {
            $userRights = array();
            foreach ($this->permissionRegistry->get(get_class($node))->all() as $permission) {
                $userRights[] = $permission->getName();
            }
        }

        $eid = $node->getNode()->getTypeId();
        $element = $this->elementService->findElement($eid);
        $elementVersion = $this->elementService->findLatestElementVersion($element);

        //$identifier = new Makeweb_Elements_Element_Identifier($eid);
        $lockQtip = '';
        /*
        #if ($lockInfo = $lockManager->getLockInformation($identifier))
        #{
        #    if ($lockInfo['lock_uid'] == MWF_Env::getUid())
        #    {
        #        $lockQtip = '<hr>Locked by me.';
        #    }
        #    else
        #    {
        #        try
        #        {
        #            $user = MWF_Core_Users_User_Peer::getByUserID($lockInfo['lock_uid']);
        #        }
        #        catch (Exception $e)
        #        {
        #            $user = MWF_Core_Users_User_Peer::getSystemUser();
        #        }
        #
        #        $lockQtip = '<hr>Locked by '.$user->getUsername().'.';
        #    }
        #}
        */

        $elementtype = $this->elementService->findElementtype($element);
        $allowedElementtypeIds = array();
        foreach ($this->elementService->findAllowedChildren($elementtype) as $allowedElementtype) {
            $allowedElementtypeIds[] = $allowedElementtype->getId();
        }

        $qtip = 'TID: ' . $node->getId() . '<br />' .
            'EID: ' . $element->getEid() . '<br />' .
            'Version: ' . $elementVersion->getVersion() . '<br />' .
            '<hr>' .
            'Element Type: ' . $elementtype->getTitle() . '<br />' .
            'Element Type Version: ' . $elementtype->getRevision() .
            $lockQtip;

        $data = array(
            'id'                  => $node->getID(),
            'eid'                 => $element->getEid(),
            'text'                => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()),
            'icon'                => $this->iconResolver->resolveNode($node, $language),
            'navigation'          => $node->getNode()->getInNavigation(),
            'restricted'          => $node->getNode()->getNeedAuthentication(),
            'element_type'        => $elementtype->getTitle(),
            'element_type_id'     => $elementtype->getId(),
            'element_type_type'   => $elementtype->getType(),
            'alias'               => $node->getTree()->isInstance($node),
            'allow_drag'          => true,
            'sort_mode'           => $node->getNode()->getSortMode(),
            'areas'               => array(355),
            'allowed_et'          => $allowedElementtypeIds,
            'is_published'        => $node->getTree()->isPublished($node, $language),
            'rights'              => $userRights,
            'qtip'                => $qtip,
            'allow_children'      => $elementtype->getHideChildren() ? false : true,
            'default_tab'         => $elementtype->getDefaultTab(),
            'default_content_tab' => $elementtype->getDefaultContentTab(),
            'masterlanguage'      => $element->getMasterLanguage()
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
