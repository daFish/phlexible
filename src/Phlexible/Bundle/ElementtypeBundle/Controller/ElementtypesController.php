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
use FOS\RestBundle\View\View;
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
 * List controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 * @Rest\NamePrefix("phlexible_api_elementtype_")
 */
class ElementtypesController extends FOSRestController
{
    /**
     * Return elementtypes
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Elementtype",
     *   section="elementtype",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getElementtypesAction()
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtypes = $elementtypeService->findAllElementtypes();

        return $this->handleView($this->view(
            array(
                'elementtypes' => array_values($elementtypes),
                'count'        => count($elementtypes)
            )
        ));

        $checker = $this->get('phlexible_element.checker');
        $changes = $checker->check();
        $hasChanges = false;
        foreach ($changes as $change) {
            if ($change->getNeedImport()) {
                $hasChanges = true;
                break;
            }
        }

        return new JsonResponse([
            'elementtypes' => $elementtypes,
            'total'        => count($elementtypes),
            'changes'      => $hasChanges,
        ]);
    }

    /**
     * Return elementtype
     *
     * @param string $elementtypeId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Elementtype",
     *   section="metaset",
     *   output="Phlexible\Bundle\ElementtypeBundle\Model\Elementtype",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when job was not found"
     *   }
     * )
     */
    public function getElementtypeAction($elementtypeId)
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype($elementtypeId);

        if (!$elementtype instanceof Elementtype) {
            throw new NotFoundHttpException('Elementtype not found');
        }

        return array(
            'elementtype' => $elementtype
        );
    }

    /**
     * Return elementtype tree
     *
     * @param string $elementtypeId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Elementtype Structure",
     *   section="elementtype",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when job was not found"
     *   }
     * )
     */
    public function getElementtypeTreeAction($elementtypeId)
    {
        $mode = 'edit';//$request->get('mode', 'edit');
        $language = $this->getUser()->getInterfaceLanguage('en');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype($elementtypeId);

        if (!$elementtype instanceof Elementtype) {
            throw new NotFoundHttpException('Elementtype not found');
        }

        $serializer = new Serializer($elementtypeService);
        $data = $serializer->serialize($elementtype, $language, $mode);

        return $data;
    }

    /**
     * Create elementtype
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create a Elementtype",
     *   section="elementtype",
     *   input="Phlexible\Bundle\ElementtypeBundle\Form\Type\ElementtypeType",
     *   statusCodes={
     *     201="Returned when elementtype was created",
     *     204="Returned when elementtype was updated",
     *     404="Returned when elementtype was not found"
     *   }
     * )
     */
    public function postElementtypesAction(Request $request)
    {
        return $this->processForm($request, new Elementtype());
    }

    /**
     * Update elementtype
     *
     * @param Request $request
     * @param string  $elementtypeId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Update a Elementtype",
     *   section="elementtype",
     *   input="Phlexible\Bundle\ElementtypeBundle\Form\Type\ElementtypeType",
     *   statusCodes={
     *     201="Returned when elementtype was created",
     *     204="Returned when elementtype was updated",
     *     404="Returned when elementtype was not found"
     *   }
     * )
     */
    public function putElementtypeAction(Request $request, $elementtypeId)
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype($elementtypeId);

        return $this->processForm($request, $elementtype);
    }

    /**
     * @param Request     $request
     * @param Elementtype $elementtype
     *
     * @return Rest\View|Response
     */
    private function processForm(Request $request, Elementtype $elementtype)
    {
        $statusCode = !$elementtype->getId() ? 201 : 204;

        $form = $this->createForm(new ElementtypeType(), $elementtype);
        $form->submit($request);

        if ($form->isValid()) {
            $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
            $elementtypeService->updateTemplate($elementtype);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'phlexible_api_elementtype_get_elementtype', array('elementtypeId' => $elementtype->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete an elementtype
     *
     * @param string $elementtypeId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Delete a Elementtype",
     *   section="elementtype",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the elementtype is not found"
     *   }
     * )
     */
    public function deleteElementtypeAction($elementtypeId)
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype($elementtypeId);

        $elementtypeService->deleteElementtype($elementtype);

        return $this->handleView($this->view(
            array(
                'success' => true
            )
        ));
    }

    /**
     * Duplicate elementtype
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/duplicate", name="elementtypes_list_duplicate")
     */
    public function duplicateAction(Request $request)
    {
        $id = $request->get('id');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $sourceElementtype = $elementtypeService->findElementtype($id);

        $elementtype = $elementtypeService->duplicateElementtype($sourceElementtype, $this->getUser()->getUsername());

        return new ResultResponse(true, 'Element type duplicated.');
    }
}
