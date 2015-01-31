<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Prefix("/search")
 * @NamePrefix("phlexible_search_")
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
     * @QueryParam(name="query", requirements=".+", description="Search query")
     * @QueryParam(name="start", requirements="\d+", default=0, description="First results")
     * @QueryParam(name="limit", requirements="\d+", default=8, description="Max results")
     * @ApiDoc
     */
    public function getResultsAction(ParamFetcher $paramFetcher)
    {
        $query = $paramFetcher->get('query');
        $limit = $paramFetcher->get('limit');
        $start = $paramFetcher->get('start');

        $search = $this->get('phlexible_search.search');
        $results = $search->search($query);

        return $this->handleView($this->view(
            array(
                'results'    => array_slice($results, $start, $limit),
                'totalCount' => count($results)
            )
        ));
    }
}
