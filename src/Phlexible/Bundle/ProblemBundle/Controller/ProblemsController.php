<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Problems controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Security("is_granted('ROLE_PROBLEMS')")
 * @Prefix("/problem")
 * @NamePrefix("phlexible_problem_")
 */
class ProblemsController extends FOSRestController
{
    /**
     * Get problems
     *
     * @return JsonResponse
     *
     * @ApiDoc()
     */
    public function getProblemsAction()
    {
        $problemFetcher = $this->get('phlexible_problem.problem_fetcher');

        $problems = $problemFetcher->fetch();

        return $this->handleView($this->view(
            array(
                'problems' => $problems,
                'count'    => count($problems)
            )
        ));

        $data = [];
        foreach ($problemFetcher->fetch() as $problem) {
            $data[] = [
                'id'            => strlen($problem->getId()) ? $problem->getId() : md5(serialize($problem)),
                'iconCls'       => $problem->getIconClass(),
                'severity'      => $problem->getSeverity(),
                'msg'           => $problem->getMessage(),
                'hint'          => $problem->getHint(),
                'link'          => $problem->getLink(),
                'createdAt'     => $problem->getCreatedAt()->format('Y-m-d H:i:s'),
                'lastCheckedAt' => $problem->getLastCheckedAt()->format('Y-m-d H:i:s'),
                'source'        => $problem->isLive() ? 'live' : 'cached',
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Get problem
     *
     * @param string $problemId
     *
     * @return JsonResponse
     *
     * @ApiDoc()
     */
    public function getProblemAction($problemId)
    {
        $problemFetcher = $this->get('phlexible_problem.problem_fetcher');

        $problems = $problemFetcher->fetch();

        foreach ($problems as $problem) {
            if ($problem->getId() === $problemId) {
                return $problem;
            }
        }

        return null;
    }
}
