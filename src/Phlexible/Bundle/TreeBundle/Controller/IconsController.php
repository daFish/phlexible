<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Icon controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/tree/icons")
 */
class IconsController extends Controller
{
    /**
     * Delivers icons.
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
                    'name' => $resource->getName(),
                    'url' => $this->generateUrl('tree_icon', array('icon' => $resource->getName())),
                );
            }
        }

        return new JsonResponse(array('icons' => $icons));
    }

    /**
     * Delivers an icon.
     *
     * @param Request $request
     * @param string  $icon
     *
     * @return Response
     * @Route("/{icon}", name="tree_icon")
     */
    public function iconAction(Request $request, $icon)
    {
        $puliDiscovery = $this->get('puli.discovery');

        $parameters = $request->query->all();

        $filename = null;
        foreach ($puliDiscovery->findByType('phlexible/node-icons') as $binding) {
            foreach ($binding->getResources() as $resource) {
                if ($resource->getName() === $icon) {
                    $filename = $resource->getFilesystemPath();
                    break 2;
                }
            }
        }

        if ($parameters) {
            $iconBuilder = $this->get('phlexible_tree.icon_builder');
            $filename = $iconBuilder->createParameterIcon($filename, $parameters);
            $mimetype = 'image/png';
        } else {
            $mimetype = 'image/gif';
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create(
                $filename,
                $mimetype,
                array(
                    'absolute_path' => true,
                    'inline' => true,
                )
            );
    }
}
