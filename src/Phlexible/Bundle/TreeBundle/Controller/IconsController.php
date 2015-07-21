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
use Symfony\Component\HttpFoundation\Response;

/**
 * Icon controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/tree/icons")
 */
class IconsController extends Controller
{
    /**
     * Delivers icons
     *
     * @param Request $request
     *
     * @return Response
     * @Route("", name="tree_icons")
     */
    public function iconsAction(Request $request)
    {
        $puliDiscovery = $this->get('puli.discovery');

        $icons = array();
        foreach ($puliDiscovery->findByType('phlexible/node-icons') as $binding) {
            foreach ($binding->getResources() as $resource) {
                $icons[] = array(
                    'icon' => $resource->getPath(),
                );
            }
        }

        return new JsonResponse($icons);
    }

    /**
     * Delivers an icon
     *
     * @param Request $request
     * @param string  $icon
     *
     * @return Response
     * @Route("/{icon}", name="tree_icon")
     */
    public function iconAction(Request $request, $icon)
    {
        $params = $request->query->all();

        $iconBuilder = $this->get('phlexible_element.icon_builder');
        $cacheFilename = $iconBuilder->getAssetPath($icon, $params);

        return $this->get('igorw_file_serve.response_factory')
            ->create(
                $cacheFilename,
                'image/png',
                array(
                    'absolute_path' => true,
                    'inline' => true,
                )
            );
    }

}
