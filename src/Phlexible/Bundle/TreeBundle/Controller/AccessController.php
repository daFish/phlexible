<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Phlexible\Component\AccessControl\Model\HierarchicalObjectIdentity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Access controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/tree/access")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class AccessController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/identities", name="tree_access_identities")
     */
    public function identitiesAction(Request $request)
    {
        $objectType = $request->get('objectType');
        $objectId = $request->get('objectId', null);

        if ($objectType === 'teaser') {
            $path = array($objectId);
        } elseif ($objectType === 'Phlexible\Bundle\TreeBundle\Node\NodeContext') {
            $tree = $this->get('phlexible_tree.tree_manager')->getByNodeId($objectId);
            $node = $tree->get($objectId);
            $identity = HierarchicalObjectIdentity::fromDomainObject($node);
        } else {
            throw new BadRequestHttpException("Unsupported object type $objectType");
        }

        $accessManager = $this->get('phlexible_access_control.access_manager');

        /*
        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');
        $permissions = array();
        foreach ($permissionRegistry->get($objectType) as $permissionCollection) {
            foreach ($permissionCollection->all() as $permission) {
                $permissions[] = $permission->getName();
            }
        }
        */

        $acl = $accessManager->findAcl($identity);

        $identities = array();

        if ($acl) {
            $resolver = $this->get('phlexible_access_control.security_resolver');

            foreach ($acl->getEntries() as $ace) {
                $identities[] = array(
                    'id'             => $ace->getId(),
                    'objectType'     => $acl->getObjectIdentity()->getType(),
                    'objectId'       => $acl->getObjectIdentity()->getIdentifier(),
                    'mask'           => $ace->getMask(),
                    'stopMask'       => $ace->getStopMask(),
                    'noInheritMask'  => $ace->getNoInheritMask(),
                    'objectLanguage' => null,
                    'securityType'   => $ace->getSecurityType(),
                    'securityId'     => $ace->getSecurityIdentifier(),
                    'securityName'   => $resolver->resolveName($ace->getSecurityType(), $ace->getSecurityIdentifier()),
                );
            }
        }

        return new JsonResponse(array('identities' => $identities));
    }
}
