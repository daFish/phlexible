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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Changes controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/tree/changes")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class ChangesController extends Controller
{
    /**
     * Return Element History.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="tree_changes")
     */
    public function indexAction(Request $request)
    {
        $nodeId = $request->get('nodeId', null);
        $action = $request->get('action', null);
        $comment = $request->get('comment', null);
        $sort = $request->get('sort', 'createdAt');
        $dir = $request->get('dir', 'DESC');
        $offset = $request->get('start', 0);
        $limit = $request->get('limit', 25);

        $criteria = array();

        if ($nodeId) {
            $criteria['nodeId'] = $nodeId;
        }
        if ($action) {
            $criteria['action'] = $action;
        }
        if ($comment) {
            $criteria['comment'] = $comment;
        }

        $changeManager = $this->get('phlexible_tree.node_change_manager');
        $count = $changeManager->countBy($criteria);

        $changes = array();
        foreach ($changeManager->findBy($criteria, array($sort => $dir), $limit, $offset) as $change) {
            $type = '-';
            if (stripos($change->getAction(), 'element')) {
                $type = 'element';
            } elseif (stripos($change->getAction(), 'node')) {
                $type = 'treeNode';
            } elseif (stripos($change->getAction(), 'teaser')) {
                $type = 'teaser';
            }

            $changes[] = array(
                'id' => $change->getId(),
                'nodeId' => $change->getNodeId(),
                'type' => $type,
                'version' => $change->getVersion(),
                'language' => $change->getLanguage(),
                'comment' => $change->getComment(),
                'action' => $change->getAction(),
                'username' => $change->getCreateUserId(),
                'createdAt' => $change->getCreatedAt()->format('Y-m-d H:i:s'),
            );
        }

        $data = array(
            'total' => $count,
            'history' => $changes,
        );

        return new JsonResponse($data);
    }
}
