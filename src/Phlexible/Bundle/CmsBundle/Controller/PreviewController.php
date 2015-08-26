<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Controller;

use Phlexible\Component\Tree\WorkingTreeContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Preview controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/frontend/preview")
 */
class PreviewController extends Controller
{
    /**
     * @param Request $request
     * @param int     $nodeId
     *
     * @return Response
     * @Route("/{_locale}/{nodeId}", name="cms_preview")
     */
    public function previewAction(Request $request, $nodeId)
    {
        $locale = $request->get('_locale');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $context = new WorkingTreeContext($locale);
        $tree = $treeManager->getByNodeId($context, $nodeId);
        $node = $tree->get($nodeId);

        $siteroot = $siterootManager->find($node->getTree()->getSiterootId());

        $request->setLocale($locale);
        $request->attributes->set('node', $node);
        $request->attributes->set('siteroot', $siteroot);
        $request->attributes->set('preview', true);

        $this->get('router.request_context')->setParameter('preview', true);

        return $this->render($node->getTemplate(), array('node' => $node, 'siteroot' => $siteroot));
    }
}
