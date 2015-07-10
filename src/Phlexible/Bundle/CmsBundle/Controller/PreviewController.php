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
     * @param int     $treeId
     *
     * @return Response
     * @Route("/{_locale}/{treeId}", name="cms_preview")
     */
    public function previewAction(Request $request, $treeId)
    {
        $language = $request->get('_locale');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $tree = $treeManager->getByTreeId($treeId);
        $tree->setLanguage($language);
        $node = $tree->get($treeId);

        $siteroot = $siterootManager->find($node->getTree()->getSiterootId());
        $siterootUrl = $siteroot->getDefaultUrl();

        $request->setLocale($language);
        $request->attributes->set('routeDocument', $node);
        $request->attributes->set('contentDocument', $node);
        $request->attributes->set('siterootUrl', $siterootUrl);
        $request->attributes->set('preview', true);

        $this->get('router.request_context')->setParameter('preview', true);
        $node->getTree()->setPreview(true);
        $versionStrategy = $this->get('phlexible_tree.mediator.preview_version_strategy');
        if ($request->query->has('version')) {
            $versionStrategy->setVersion($request->query->get('version'));
        }
        $node->getTree()->getMediator()->setVersionStrategy($versionStrategy);

        $configurator = $this->get('phlexible_cms.configurator');
        $configuration = $configurator->configure($request);
        if ($configuration->hasResponse()) {
            return $configuration->getResponse();
        }

        $data = $configuration->getVariables();

        return $this->render($data['template'], (array) $data);
    }
}
