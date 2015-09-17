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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Types controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/tree/types")
 */
class TypesController extends Controller
{
    /**
     * List all types.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="tree_types")
     */
    public function typesAction(Request $request)
    {
        $nodeTypeManager = $this->get('phlexible_tree.node_type_manager');

        $types = array();
        foreach ($nodeTypeManager->getTypes() as $name => $type) {
            $types[$name] = array(
                'name' => $name,
                'icon' => '',
                'type' => $type,
            );
        }

        ksort($types);
        $types = array_values($types);

        return new JsonResponse($types);
    }
}
