<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Types controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/tree/types")
 */
class TypesController extends Controller
{
    /**
     * List all types
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
