<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Problems controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_PROBLEMS')")
 * @Rest\NamePrefix("phlexible_api_problem_")
 */
class ProblemsController extends FOSRestController
{
    /**
     * Get problems
     *
     * @return JsonResponse
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Problem",
     *   section="problem",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getProblemsAction()
    {
        $problemFetcher = $this->get('phlexible_problem.problem_fetcher');

        $problems = $problemFetcher->fetch();

        return array(
            'problems' => $problems,
            'count'    => count($problems)
        );
    }

    /**
     * Get problem
     *
     * @param string $problemId
     *
     * @return JsonResponse
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Problem",
     *   section="problem",
     *   output="Phlexible\Bundle\ProblemBundle\Entity\Problem",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when problem was not found"
     *   }
     * )
     */
    public function getProblemAction($problemId)
    {
        $problemFetcher = $this->get('phlexible_problem.problem_fetcher');

        $problems = $problemFetcher->fetch();

        foreach ($problems as $problem) {
            if ($problem->getId() === $problemId) {
                return array('problem' => $problem);
            }
        }

        throw new NotFoundHttpException('User not found');
    }
}
