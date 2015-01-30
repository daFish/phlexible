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
     * @ApiDoc()
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

        $data = [];
        foreach ($jobs as $queueItem) {
            $data[] = [
                'id'          => $queueItem->getId(),
                'command'     => $queueItem->getCommand(),
                'priority'    => $queueItem->getPriority(),
                'status'      => $queueItem->getState(),
                'create_time' => $queueItem->getCreatedAt()->format('Y-m-d H:i:s'),
                'start_time'  => $queueItem->getStartedAt() ? $queueItem->getStartedAt()->format('Y-m-d H:i:s') : null,
                'end_time'    => $queueItem->getFinishedAt() ? $queueItem->getFinishedAt()->format('Y-m-d H:i:s') : null,
                'output'      => nl2br($queueItem->getOutput()),
            ];
        }

        return new JsonResponse(['data' => $data]);
    }

    /**
     * Get job
     *
     * @param string $jobId
     *
     * @return Response
     *
     * @View()
     * @ApiDoc()
     */
    public function getJobAction($jobId)
    {
        $jobManager = $this->get('phlexible_queue.job_manager');

        $job = $jobManager->find($jobId);

        return $job;
    }

}
