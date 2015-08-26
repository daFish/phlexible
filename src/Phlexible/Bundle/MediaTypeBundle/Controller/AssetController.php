<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTypeBundle\Controller;

use Phlexible\Bundle\MediaTypeBundle\Compiler\CssCompiler;
use Phlexible\Bundle\MediaTypeBundle\Compiler\ScriptCompiler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatypes/asset")
 * @Security("is_granted('ROLE_BACKEND')")
 */
class AssetController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/scripts", name="phlexible_mediatype_asset_scripts")
     */
    public function scriptsAction(Request $request)
    {
        $mediaClassifier = $this->get('phlexible_media.media_classifier');
        $translator = $this->get('translator');

        $data = array();
        foreach ($mediaClassifier->getCollection()->all() as $mediaType) {
            $key = str_replace(':', '-', (string) $mediaType);
            $item = array(
                'cls' => sprintf('p-mediatype-%s', $key),
                'de'  => $translator->trans($key, array(), 'mediatypes', 'de'),
                'en'  => $translator->trans($key, array(), 'mediatypes', 'en'),
            );

            $data[(string) $mediaType] = $item;
        }

        $content = sprintf('Ext.onReady(function () {Phlexible.mediatype.MediaTypes.setData(%s);});', json_encode($data));

        return new Response($content, 200, array('Content-type' => 'text/javascript'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/css", name="phlexible_mediatype_asset_css")
     */
    public function cssAction(Request $request)
    {
        $mediaClassifier = $this->get('phlexible_media.media_classifier');
        $basePath = $request->getBasePath();
        $baseUrl = $request->getBaseUrl();

        $compiler = new CssCompiler();

        $css = $compiler->compile($mediaClassifier->getCollection());
        $css = str_replace(
            array('/BASE_PATH/', '/BASE_URL/', '/BUNDLES_PATH/'),
            array($basePath, $baseUrl, $basePath . 'bundles/'),
            $css
        );

        return new Response($css, 200, array('Content-type' => 'text/css'));
    }
}
