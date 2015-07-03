<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Usage controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 * @Rest\NamePrefix("phlexible_api_elementtype_")
 */
class UsagesController extends FOSRestController
{
    /**
     * Return Usage of an Element Type
     *
     * @param string $elementtypeId
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Elementtype Usage",
     *   section="elementtype",
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getUsagesAction($elementtypeId)
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $usageManager = $this->get('phlexible_elementtype.usage_manager');

        $elementtype = $elementtypeService->findElementtype($elementtypeId);

        if (!$elementtype instanceof Elementtype) {
            throw new NotFoundHttpException('Elementtype not found');
        }

        $usages = array();
        foreach ($usageManager->getUsage($elementtype) as $usage) {
            $usages[] = array(
                'type'           => $usage->getType(),
                'as'             => $usage->getAs(),
                'id'             => $usage->getId(),
                'title'          => $usage->getTitle(),
                'latest_version' => $usage->getLatestVersion(),
            );
        }

        return array(
            'usages' => $usages,
            'total'  => count($usages)
        );
    }
}
