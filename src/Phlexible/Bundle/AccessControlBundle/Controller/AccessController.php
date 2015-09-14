<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\AccessControl\Domain\Entry;
use Phlexible\Component\AccessControl\Exception\InvalidArgumentException;
use Phlexible\Component\AccessControl\Model\HierarchicalObjectIdentity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Access controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/accesscontrol")
 * @Security("is_granted('ROLE_ACCESS_CONTROL')")
 */
class AccessController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/users", name="accesscontrol_users")
     */
    public function usersAction(Request $request)
    {
        $query  = $request->get('query');
        $limit  = $request->get('limit', 20);
        $offset = $request->get('start', 0);

        $userProvider = $this->get('phlexible_access_control.user_security_provider');

        $data = $userProvider->getAll($query, $limit, $offset);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/groups", name="accesscontrol_groups")
     */
    public function groupsAction(Request $request)
    {
        $query  = $request->get('query');
        $limit  = $request->get('limit', 20);
        $offset = $request->get('start', 0);

        $userProvider = $this->get('phlexible_access_control.group_security_provider');

        $data = $userProvider->getAll($query, $limit, $offset);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/permissions", name="accesscontrol_permissions")
     */
    public function permissionsAction(Request $request)
    {
        $objectType = $request->get('objectType');

        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');
        $permissions = array();
        foreach ($permissionRegistry->get($objectType)->all() as $permission) {
            $permissions[] = array(
                'name'    => $permission->getName(),
                'bit'     => $permission->getBit(),
                'iconCls' => 'null',
            );
        }

        return new JsonResponse(array('permissions' => $permissions));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @throws InvalidArgumentException
     * @Route("/save", name="accesscontrol_save")
     */
    public function saveAction(Request $request)
    {
        $objectType = $request->get('objectType');
        $objectId   = $request->get('objectId');
        $data       = $request->get('identities');

        if (!$data) {
            throw new InvalidArgumentException('No save data.');
        }

        $identities = json_decode($data, true);

        if ($objectType === 'Phlexible\Bundle\TreeBundle\Node\NodeContext') {
            $tree = $this->get('phlexible_tree.tree_manager')->getByNodeId($objectId);
            $node = $tree->get($objectId);
            $objectIdentity = HierarchicalObjectIdentity::fromDomainObject($node);
        } elseif ($objectType === 'Phlexible\Bundle\MediaManagerBundle\Entity\Folder') {
            $volume = $this->get('phlexible_media_manager.volume_manager')->getByFolderId($objectId);
            $folder = $volume->findFolder($objectId);
            $objectIdentity = HierarchicalObjectIdentity::fromDomainObject($folder);
        } else {
            throw new \Exception("Unsupported object type $objectType");
        }

        $accessManager = $this->get('phlexible_access_control.access_manager');
        $acl = $accessManager->findAcl($objectIdentity);

        if (!$acl) {
            $acl = $accessManager->createAcl($objectIdentity);
        }

        foreach ($acl->getEntries() as $ace) {
            $acl->removeEntry($ace);
        }
        foreach ($identities as $objectIdentity) {
            $ace = new Entry(
                $acl,
                $objectIdentity['securityType'],
                $objectIdentity['securityId'],
                $objectIdentity['mask'],
                $objectIdentity['stopMask'],
                $objectIdentity['noInheritMask']
            );

            $acl->addEntry($ace);
        }

        $accessManager->updateAcl($acl);

        return new ResultResponse(true, 'Rights saved.');
    }
}
