<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendBundle\Controller;

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
     * @Route("/{_locale}/{treeId}", name="frontend_preview")
     */
    public function previewAction(Request $request, $treeId)
    {
        $language = $request->get('_locale');
        $tid = $request->get('id');

        $contentTreeManager = $this->get('phlexible_tree.content_tree_manager.delegating');
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $tree = $contentTreeManager->findByTreeId($treeId);
        $tree->setLanguage($language);
        $node = $tree->get($treeId);

        $siteroot = $siterootManager->find($node->getTree()->getSiterootId());
        $siterootUrl = $siteroot->getDefaultUrl();

        $request->setLocale($language);
        $request->attributes->set('routeDocument', $node);
        $request->attributes->set('contentDocument', $node);
        $request->attributes->set('siterootUrl', $siterootUrl);
        $request->attributes->set('preview', true);

        $node->getTree()->setPreview(true);
        $this->get('router.request_context')->setParameter('preview', true);

        $configurator = $this->get('phlexible_element_renderer.configurator');
        $configuration = $configurator->configure($request);
        if ($configuration->hasResponse()) {
            return $configuration->getResponse();
        }

        $data = $configuration->getVariables();

        return $this->render($data['template'], (array) $data);
    }
}
