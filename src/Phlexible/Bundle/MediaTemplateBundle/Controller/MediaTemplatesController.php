<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\MediaTemplateBundle\Form\Type\ImageTemplateType;
use Phlexible\Component\MediaTemplate\Domain\ImageTemplate;
use Phlexible\Component\MediaTemplate\Domain\TemplateCollection;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Media templates controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA_TEMPLATES')")
 * @Rest\NamePrefix("phlexible_api_mediatemplate_")
 */
class MediaTemplatesController extends FOSRestController
{
    /**
     * Get media templates.
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of MediaTemplate",
     *   section="mediatemplate",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getMediatemplatesAction()
    {
        $mediaTemplateManager = $this->get('phlexible_media_template.template_manager');
        $mediaTemplates = $mediaTemplateManager->findAll();

        return new TemplateCollection(
            $mediaTemplates,
            count($mediaTemplates)
        );
    }

    /**
     * Get media template.
     *
     * @param string $mediaTemplateId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a MediaTemplate",
     *   section="mediatemplate",
     *   output="Phlexible\Component\MediaTemplate\Model\TemplateInterface",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when mediatemplate was not found"
     *   }
     * )
     */
    public function getMediatemplateAction($mediaTemplateId)
    {
        $mediaTemplateManager = $this->get('phlexible_media_template.template_manager');
        $mediaTemplate = $mediaTemplateManager->find($mediaTemplateId);

        if (!$mediaTemplate instanceof TemplateInterface) {
            throw new NotFoundHttpException('Media template not found');
        }

        return $mediaTemplate;
    }

    /**
     * Create media template.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create a MediaTemplate",
     *   section="mediatemplate",
     *   input="Phlexible\Bundle\MediaTemplateBundle\Form\Type\MediaTemplateType",
     *   statusCodes={
     *     201="Returned when mediatemplate was created",
     *     204="Returned when mediatemplate was updated",
     *     404="Returned when mediatemplate was not found"
     *   }
     * )
     */
    public function postMediatemplatesAction(Request $request)
    {
        return $this->processForm($request);
    }

    /**
     * Update media template.
     *
     * @param Request $request
     * @param string  $mediaTemplateId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Update a MediaTemplate",
     *   section="mediatemplate",
     *   input="Phlexible\Bundle\MediaTemplateBundle\Form\Type\MediaTemplateType",
     *   statusCodes={
     *     201="Returned when mediatemplate was created",
     *     204="Returned when mediatemplate was updated",
     *     404="Returned when mediatemplate was not found"
     *   }
     * )
     */
    public function putMediatemplateAction(Request $request, $mediaTemplateId)
    {
        $mediaTemplateManager = $this->get('phlexible_media_template.template_manager');
        $mediaTemplate = $mediaTemplateManager->find($mediaTemplateId);

        if (!$mediaTemplate instanceof TemplateInterface) {
            throw new NotFoundHttpException('Media template not found');
        }

        return $this->processForm($request, $mediaTemplate);
    }

    /**
     * @param Request           $request
     * @param TemplateInterface $mediaTemplate
     *
     * @return Rest\View|Response
     */
    private function processForm(Request $request, TemplateInterface $mediaTemplate = null)
    {
        $statusCode = !$mediaTemplate ? 201 : 204;

        /*
        switch ($request->get('mediaTemplate')['type']) {
            case 'image':
                $formType = new ImageTemplateType();
                break;
            case 'video':
                $formType = new VideoTemplateType();
                break;
            case 'audio':
                $formType = new AudioTemplateType();
                break;
            default:
                throw new BadRequestHttpException("Invalid or missing type.");
        }
        */

        $formType = new ImageTemplateType();
        $mediaTemplate = new ImageTemplate();
        $form = $this->createForm($formType, $mediaTemplate);
        $form->submit($request);

        if ($form->isValid()) {
            $mediaTemplateManager = $this->get('phlexible_media_template.template_manager');
            $mediaTemplateManager->updateTemplate($mediaTemplate);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'phlexible_api_mediatemplate_get_mediatemplate', array('mediaTemplateId' => $mediaTemplate->getKey()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete media template.
     *
     * @param string $mediaTemplateId
     *
     * @return Response
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Delete a MediaTemplate",
     *   section="mediatemplate",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the mediatemplate is not found"
     *   }
     * )
     */
    public function deleteMediatemplateAction($mediaTemplateId)
    {
        $mediaTemplateManager = $this->get('phlexible_media_template.template_manager');
        $mediaTemplate = $mediaTemplateManager->find($mediaTemplateId);

        if (!$mediaTemplate instanceof TemplateInterface) {
            throw new NotFoundHttpException('MediaTemplate not found');
        }

        $mediaTemplateManager->deleteMediaTemplate($mediaTemplate);
    }
}
