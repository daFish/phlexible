<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\MessageBundle\Entity\Filter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Filters controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Prefix("/message")
 * @NamePrefix("phlexible_message_")
 */
class FiltersController extends FOSRestController
{
    /**
     * Get filters
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     *
     * @QueryParam(name="userId", requirements=".+", description="User ID")
     */
    public function getFiltersAction(ParamFetcher $paramFetcher)
    {
        $filterManager = $this->get('phlexible_message.filter_manager');

        $criteria = array();
        if ($userId = $paramFetcher->get('userId')) {
            $criteria['userId'] = $userId;
        }

        $filters = $filterManager->findBy($criteria);

        return $this->handleView($this->view(
            array(
                'users' => $filters,
                'count' => count($filters)
            )
        ));
    }

    /**
     * List filter values
     *
     * @return JsonResponse
     * @Route("/facets", name="messages_filter_facets")
     */
    public function facetsAction()
    {
        $messageManager = $this->get('phlexible_message.message_manager');
        $data = $messageManager->getFacets();

        $data['priorityNames'] = $messageManager->getPriorityNames();
        $data['typeNames'] = $messageManager->getTypeNames();

        return new JsonResponse($data);
    }

    /**
     * Create filter
     *
     * @param Filter $filter
     *
     * @return Response
     *
     * @ParamConverter("subscription", converter="fos_rest.request_body")
     * @Post("/filters")
     * @ApiDoc()
     */
    public function postFiltersAction(Filter $filter)
    {
        $filterManager = $this->get('phlexible_message.filter_manager');
        $filterManager->updateFilter($filter);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }

    /**
     * Updates a Filter
     *
     * @param Filter $filter
     * @param string $filterId
     *
     * @return Response
     * @ParamConverter("filter", converter="fos_rest.request_body")
     * @Put("/filters/{filterId}")
     * @ApiDoc()
     */
    public function putFilterAction(Filter $filter, $filterId)
    {
        $filterManager = $this->get('phlexible_message.filter_manager');

        $filter = $filterManager->find($filterId);

        $filterManager->updateFilter($filter);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }

    /**
     * Delete filter
     *
     * @param string $filterId
     *
     * @return Response
     * @ApiDoc()
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
