<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Icon controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/icon")
 */
class IconController extends Controller
{
    /**
     * Delivers an icon
     *
     * @param Request $request
     * @param string  $icon
     *
     * @return Response
     * @Route("/{icon}", name="elements_icon")
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
