<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bundles controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_BUNDLES')")
 * @Prefix("/gui")
 * @NamePrefix("phlexible_gui_")
 */
class BundlesController extends FOSRestController
{
    /**
     * Get bundles
     *
     * @return Response
     * @ApiDoc()
     */
    public function getBundlesAction()
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        $bundlesData = [];
        $packages = [];

        foreach ($bundles as $id => $class) {
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

            $bundlesData[$id] = [
                'id'          => $id,
                'classname'   => $className,
                'package'     => $package,
                'path'        => $path,
            ];

            $namespace = $reflection->getNamespaceName();
            $package = current(explode('\\', $namespace));
            $packages[$package] = 1;
        }

        ksort($bundlesData);
        $bundlesData = array_values($bundlesData);

        $packagesData = [];
        foreach (array_keys($packages) as $package) {
            $packages[$package] = ['id' => $package, 'title' => ucfirst($package), 'checked' => true];
        }
        ksort($packagesData);
        $packagesData = array_values($packagesData);

        return $this->handleView($this->view(
            array(
                'users' => $bundlesData,
                'count' => count($bundlesData),
                'packages' => $packagesData,
            )
        ));
    }
}
