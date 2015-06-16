<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTypeBundle\Controller;

use Phlexible\Bundle\MediaTypeBundle\Compiler\CssCompiler;
use Phlexible\Bundle\MediaTypeBundle\Compiler\ScriptCompiler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatypes/asset")
 * @Security("is_granted('ROLE_MEDIA_TYPES')")
 */
class AssetController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/mediatypes", name="mediatypes_asset_mediatypes")
     */
    public function mediatypesAction(Request $request)
    {
        $mediaClassifier = $this->get('phlexible_media.media_classifier');
        $translator = $this->get('translator');

        $data = array();
        foreach ($mediaClassifier->getCollection()->all() as $mediaType) {
            $key = str_replace(':', '-', (string) $mediaType);
            $item = [
                'cls' => sprintf('p-mediatype-%s', $key),
                'de'  => $translator->trans($key, array(), 'mediatypes', 'de'),
                'en'  => $translator->trans($key, array(), 'mediatypes', 'en'),
            ];

            $data[(string) $mediaType] = $item;
        }

        $content = sprintf('Ext.onReady(function () {Phlexible.mediatype.MediaTypes.setData(%s);});', json_encode($data));

        return new Response($content, 200, array('Content-type' => 'text/javascript'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/css", name="mediatypes_asset_css")
     */
    public function cssAction(Request $request)
    {
        $mediaClassifier = $this->get('phlexible_media.media_classifier');
        $basePath = $request->getBasePath();
        $baseUrl = $request->getBaseUrl();

        $compiler = new CssCompiler();

        $css = $compiler->compile($mediaClassifier->getCollection());
        $css = str_replace(
            ['/BASE_PATH/', '/BASE_URL/', '/BUNDLES_PATH/'],
            [$basePath, $baseUrl, $basePath . 'bundles/'],
            $css
        );

        return new Response($css, 200, array('Content-type' => 'text/css'));
    }
}
