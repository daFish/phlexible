<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\QueueBundle\Entity\Job;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Security("is_granted('ROLE_QUEUE')")
 * @Prefix("/queue")
 * @NamePrefix("phlexible_queue_")
 */
class JobsController extends FOSRestController
{
    /**
     * Get jobs
     *
     * @return Response
     *
     * @ApiDoc
     */
    public function getJobsAction()
    {
        $jobManager = $this->get('phlexible_queue.job_manager');

        $jobs = $jobManager->findBy([], ['createdAt' => 'DESC']);

        return $this->handleView($this->view(
            array(
                'jobs' => $jobs,
                'count' => count($jobs)
            )
        ));
    }

    /**
     * Get job
     *
     * @param string $jobId
     *
     * @return Response
     *
     * @View
     * @ApiDoc
     */
    public function getJobAction($jobId)
    {
        $jobManager = $this->get('phlexible_queue.job_manager');

        $job = $jobManager->find($jobId);

        return $job;
    }

    /**
     * Create job
     *
     * @param Job $job
     *
     * @return Response
     *
     * @ParamConverter("job", converter="fos_rest.request_body")
     * @ApiDoc
     */
    public function postJobsAction(Job $job)
    {
        $jobManager = $this->get('phlexible_queue.job_manager');

        $jobManager->updateJob($job);

        return $this->handleView($this->view(
            array(
                'success' => 1,
            )
        ));
    }
}
