<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Component\MediaTemplate\Model\MediaTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Media templates controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA_TEMPLATES')")
 * @Rest\NamePrefix("phlexible_api_mediatemplate_")
 */
class MediaTemplatesController extends FOSRestController
{
    /**
     * Get media templates
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

        return array(
            'mediatemplates' => array_values($mediaTemplates),
            'count'          => count($mediaTemplates),
        );
    }

    /**
     * Get media template
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

        return array(
            'mediatemplate' => $mediaTemplate,
        );
    }

    /**
     * Create media template
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
        return $this->processForm($request, new MediaTemplate());
    }

    /**
     * Update media template
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
    private function processForm(Request $request, TemplateInterface $mediaTemplate)
    {
        $statusCode = !$mediaTemplate->getId() ? 201 : 204;

        $form = $this->createForm(new MediaTemplateType(), $mediaTemplate);
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
                        'phlexible_api_mediatemplate_get_mediatemplate', array('mediaTemplateId' => $mediaTemplate->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete media template
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
