<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/gui/asset")
 */
class AssetController extends Controller
{
    /**
     * Output scripts
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     * @Route("/scripts", name="phlexible_gui_asset_scripts")
     */
    public function scriptsAction(Request $request)
    {
        $scriptsBuilder = $this->get('phlexible_gui.asset.builder.scripts');
        try {
            $file = $scriptsBuilder->build();
        } catch (\Exception $e) {
            if (!in_array('text/html', $request->getAcceptableContentTypes())) {
                return new Response("alert('" . str_replace("'", "\'", $e->getMessage()) . "');");
            } else {
                throw $e;
            }
        }

        return new BinaryFileResponse($file, 200, array('Content-Type' => 'text/javascript'));
    }

    /**
     * Output css
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/css", name="phlexible_gui_asset_css")
     */
    public function cssAction(Request $request)
    {
        $cssBuilder = $this->get('phlexible_gui.asset.builder.css');
        $file = $cssBuilder->build($request->getBaseUrl(), $request->getBasePath());

        return new BinaryFileResponse($file, 200, array('Content-Type' => 'text/css;charset=UTF-8'));
    }

    /**
     * Output translations
     *
     * @param Request $request
     * @param string  $language
     *
     * @return Response
     * @Route("/translations/{language}", name="phlexible_gui_asset_translations")
     */
    public function translationsAction(Request $request, $language)
    {
        $translationBuilder = $this->get('phlexible_gui.asset.builder.translations');
        $file = $translationBuilder->build($language);

        return new BinaryFileResponse($file, 200, array('Content-Type' => 'text/javascript;charset=UTF-8'));
    }
}
