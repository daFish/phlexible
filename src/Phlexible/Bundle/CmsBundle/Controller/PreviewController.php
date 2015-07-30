<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Controller;

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
        $language = $request->get('_locale');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $tree = $treeManager->getByNodeId($nodeId);
        $tree->setDefaultLanguage($language);
        $node = $tree->get($nodeId);

        $siteroot = $siterootManager->find($node->getTree()->getSiterootId());

        $request->setLocale($language);
        $request->attributes->set('node', $node);
        $request->attributes->set('siteroot', $siteroot);
        $request->attributes->set('preview', true);

        $this->get('router.request_context')->setParameter('preview', true);
        $elementMediator = $this->get('phlexible_tree.mediator.element');
        $versionStrategy = $this->get('phlexible_tree.mediator.preview_version_strategy');
        if ($request->query->has('version')) {
            $versionStrategy->setVersion($request->query->get('version'));
        }
        $elementMediator->setVersionStrategy($versionStrategy);

        return $this->render($node->getTemplate(), array('node' => $node, 'siteroot' => $siteroot));
    }
}
