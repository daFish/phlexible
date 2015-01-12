<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Bundles controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Route("/gui/bundles")
 * @Security("is_granted('ROLE_BUNDLES')")
 */
class BundlesController extends Controller
{
    /**
     * List all Components
     *
     * @return JsonResponse
     * @Route("", name="gui_bundles")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Returns a list of installed bundles"
     * )
     */
    public function listAction()
    {
        $modules = [];

        $components = $this->container->getParameter('kernel.bundles');

        foreach ($components as $id => $class) {
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

            $modules[$id] = [
                'id'          => $id,
                'classname'   => $className,
                'package'     => $package,
                'path'        => $path,
            ];
        }

        ksort($modules);
        $modules = array_values($modules);

        return new JsonResponse($modules);
    }

    /**
     * Filter values
     *
     * @return JsonResponse
     * @Route("/filtervalues", name="gui_bundles_filtervalues")
     * @Method("GET")
     * @ApiDoc(
     *   description="Returns a list of bundle filter values"
     * )
     */
    public function filtervaluesAction()
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        $packageList = [];
        foreach ($bundles as $id => $class) {
            $reflection = new \ReflectionClass($class);
            $namespace = $reflection->getNamespaceName();
            $package = current(explode('\\', $namespace));
            $packageList[$package] = 1;
        }

        $packages = [];
        foreach (array_keys($packageList) as $package) {
            $packages[$package] = ['id' => $package, 'title' => ucfirst($package), 'checked' => true];
        }
        ksort($packages);
        $packages = array_values($packages);

        return new JsonResponse([
            'packages' => $packages,
        ]);
    }
}
