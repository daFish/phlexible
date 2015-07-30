<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Render controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/_teaser/render")
 */
class RenderController extends Controller
{
    /**
     * Render action
     *
     * @param Request $request
     * @param int     $teaserId
     *
     * @return Response
     * @Route("/{_locale}/{teaserId}", name="teaser_render")
     */
    public function htmlAction(Request $request, $teaserId)
    {
        return new Response($teaserId);
        $treeManager = $this->get('phlexible_tree.tree_manager');

        $tree = $treeManager->getByNodeId($teaserId);
        $teaser = $tree->get($teaserId);

        $request->attributes->set('teaser', $teaser);

        return $this->render($teaser->getTemplate(), array('teaser' => $teaser));
    }
}
