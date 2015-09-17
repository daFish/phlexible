<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\MessageBundle\Form\Type\FilterType;
use Phlexible\Component\MessageFilter\Domain\Filter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Filters controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MESSAGE_FILTERS')")
 * @Rest\NamePrefix("phlexible_api_message_")
 */
class FiltersController extends FOSRestController
{
    /**
     * Get filters.
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     *
     * @Rest\QueryParam(name="userId", requirements=".+", description="User ID")
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Filter",
     *   section="message",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getFiltersAction(ParamFetcher $paramFetcher)
    {
        $filterManager = $this->get('phlexible_message.filter_manager');

        $criteria = array();
        if ($userId = $paramFetcher->get('userId')) {
            $criteria['userId'] = $userId;
        }

        $filters = $filterManager->findBy($criteria);

        return array(
            'filters' => $filters,
            'count' => count($filters),
        );
    }

    /**
     * Get filter.
     *
     * @param string $filterId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Filter",
     *   section="message",
     *   output="Phlexible\Bundle\MessageBundle\Entity\Filter",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when filter was not found"
     *   }
     * )
     */
    public function getFilterAction($filterId)
    {
        $filterManager = $this->get('phlexible_message.filter_manager');
        $filter = $filterManager->find($filterId);

        if (!$filter instanceof Filter) {
            throw new NotFoundHttpException('Filter not found');
        }

        return array(
            'filter' => $filter,
        );
    }

    /**
     * Create filter.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create a Filter",
     *   section="message",
     *   input="Phlexible\Bundle\MessageBundle\Form\Type\FilterType",
     *   statusCodes={
     *     201="Returned when filter was created",
     *     204="Returned when filter was updated",
     *     404="Returned when filter was not found"
     *   }
     * )
     */
    public function postFiltersAction(Request $request)
    {
        return $this->processForm($request, new Filter());
    }

    /**
     * Update filter.
     *
     * @param Request $request
     * @param string  $filterId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Update a Filter",
     *   section="message",
     *   input="Phlexible\Bundle\MessageBundle\Form\Type\FilterType",
     *   statusCodes={
     *     201="Returned when filter was created",
     *     204="Returned when filter was updated",
     *     404="Returned when filter was not found"
     *   }
     * )
     */
    public function putFilterAction(Request $request, $filterId)
    {
        $filterManager = $this->get('phlexible_message.filter_manager');
        $filter = $filterManager->find($filterId);

        if (!$filter instanceof Filter) {
            throw new NotFoundHttpException('Filter not found');
        }

        return $this->processForm($request, $filter);
    }

    /**
     * @param Request                                          $request
     * @param \Phlexible\Component\MessageFilter\Domain\Filter $filter
     *
     * @return Rest\View|Response
     */
    private function processForm(Request $request, Filter $filter)
    {
        $statusCode = !$filter->getId() ? 201 : 204;

        $form = $this->createForm(new FilterType(), $filter);
        $form->submit($request);

        if ($form->isValid()) {
            $filterManager = $this->get('phlexible_message.filter_manager');
            $filterManager->updateFilter($filter);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set(
                    'Location',
                    $this->generateUrl(
                        'phlexible_api_message_get_filter',
                        array('filterId' => $filter->getId()),
                        true
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete filter.
     *
     * @param string $filterId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Delete a Filter",
     *   section="message",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when message is not found"
     *   }
     * )
     */
    public function deleteFilterAction($filterId)
    {
        $filterManager = $this->get('phlexible_message.filter_manager');
        $filter = $filterManager->find($filterId);
        $filterManager->deleteFilter($filter);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }
}
