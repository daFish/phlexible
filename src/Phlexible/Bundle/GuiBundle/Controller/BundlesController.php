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

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bundles controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_BUNDLES')")
 * @Rest\NamePrefix("phlexible_api_gui_")
 */
class BundlesController extends FOSRestController
{
    /**
     * Get bundles.
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Bundle",
     *   section="gui",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getBundlesAction()
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        $bundlesData = array();

        foreach ($bundles as $name => $class) {
            $className = $class;
            $package = $class;
            if (strstr($class, '\\')) {
                $namespaceParts = explode('\\', $class);
                $package = current($namespaceParts);
            } elseif (strstr($class, '_')) {
                $namespaceParts = explode('_', $class);
                $package = current($namespaceParts);
            }

            $reflection = new \ReflectionClass($class);
            $path = $reflection->getFileName();

            $bundlesData[$name] = array(
                'name' => $name,
                'classname' => $className,
                'package' => $package,
                'path' => $path,
            );
        }

        ksort($bundlesData);
        $bundlesData = array_values($bundlesData);

        return array(
            'bundles' => $bundlesData,
            'count' => count($bundlesData),
        );
    }
}
