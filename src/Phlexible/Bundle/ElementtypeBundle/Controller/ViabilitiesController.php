<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Viability controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 * @Rest\NamePrefix("phlexible_api_elementtype_")
 */
class ViabilitiesController extends FOSRestController
{
    /**
     * List Element Types
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/fortype", name="elementtypes_viability_for_type")
     */
    public function fortypeAction(Request $request)
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');

        $for = $request->get('type', Elementtype::TYPE_FULL);

        $elementtypes = $elementtypeService->findAllElementtypes();

        $allowedForFull = $allowedForStructure = $allowedForArea = [
            Elementtype::TYPE_FULL,
            Elementtype::TYPE_STRUCTURE
        ];
        $allowedForContainer = $allowedForPart = [
            Elementtype::TYPE_LAYOUTAREA,
            Elementtype::TYPE_LAYOUTCONTAINER
        ];

        $list = [];
        foreach ($elementtypes as $elementtype) {
            $type = $elementtype->getType();

            if ($for == Elementtype::TYPE_FULL && !in_array($type, $allowedForFull)) {
                continue;
            } elseif ($for == Elementtype::TYPE_STRUCTURE && !in_array($type, $allowedForStructure)) {
                continue;
            } elseif ($for == Elementtype::TYPE_REFERENCE) {
                continue;
            } elseif ($for == Elementtype::TYPE_LAYOUTAREA && !in_array($type, $allowedForArea)) {
                continue;
            } elseif ($for == Elementtype::TYPE_LAYOUTCONTAINER && !in_array($type, $allowedForContainer)) {
                continue;
            } elseif ($for == Elementtype::TYPE_PART && !in_array($type, $allowedForPart)) {
                continue;
            }

            $list[] = [
                'id'      => $elementtype->getId(),
                'type'    => $elementtype->getType(),
                'title'   => $elementtype->getTitle(),
                'icon'    => $elementtype->getIcon(),
                'version' => $elementtype->getRevision()
            ];

        }

        return new JsonResponse(['elementtypes' => $list, 'total' => count($list)]);
    }

    /**
     * @param string $elementtypeId

     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of ElementtypeViability",
     *   section="elementtype",
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getViabilitiesAction($elementtypeId)
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $viabilityManager = $this->get('phlexible_elementtype.viability_manager');
        $elementtype = $elementtypeService->findElementtype($elementtypeId);

        if (!$elementtype instanceof Elementtype) {
            throw new NotFoundHttpException('Elementtype not found');
        }

        $viabilities = [];
        foreach ($viabilityManager->findAllowedParents($elementtype) as $viability) {
            $viabilityElementtype = $elementtypeService->findElementtype($viability->getUnderElementtypeId());
            $viabilities[] = [
                'id'    => $viabilityElementtype->getId(),
                'title' => $viabilityElementtype->getTitle(),
                'icon'  => $viabilityElementtype->getIcon()
            ];
        }

        return array(
            'viabilities' => $viabilities,
            'total'       => count($viabilities)
        );
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="elementtypes_viability_save")
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');
        $ids = $request->get('ids');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype($id);

        $elementtypeService->updateViability($elementtype, $ids);

        return new ResultResponse(true);
    }
}
