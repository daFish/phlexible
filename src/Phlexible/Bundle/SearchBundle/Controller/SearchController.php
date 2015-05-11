<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Search controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Rest\NamePrefix("phlexible_api_search_")
 */
class SearchController extends FOSRestController
{
    /**
     * Get search results
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return JsonResponse
     *
     * @Rest\QueryParam(name="query", requirements=".+", description="Search query")
     * @Rest\QueryParam(name="start", requirements="\d+", default=0, description="First results")
     * @Rest\QueryParam(name="limit", requirements="\d+", default=8, description="Max results")
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Search Results",
     *   section="search",
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getResultsAction(ParamFetcher $paramFetcher)
    {
        $query = $paramFetcher->get('query');
        $limit = $paramFetcher->get('limit');
        $start = $paramFetcher->get('start');

        $search = $this->get('phlexible_search.search');
        $results = $search->search($query);

        return array(
            'results'    => array_slice($results, $start, $limit),
            'totalCount' => count($results)
        );
    }
}
