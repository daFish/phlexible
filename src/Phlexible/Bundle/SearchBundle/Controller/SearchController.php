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
use FOS\RestBundle\Controller\FOSRestController;
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
     * Return search results
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @ApiDoc(
     *   description="Search",
     *   requirements={
     *     {"name"="query", "dataType"="string", "required"=true, "description"="Search query"}
     *   },
     *   filters={
     *     {"name"="limit", "dataType"="integer", "default"=8, "description"="Limit results"},
     *     {"name"="start", "dataType"="integer", "default"=0, "description"="Result offset"}
     *   }
     * )
     */
    public function getResultsAction(Request $request)
    {
        $query = $request->get('query');
        $limit = $request->get('limit', 8);
        $start = $request->get('start', 0);

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
