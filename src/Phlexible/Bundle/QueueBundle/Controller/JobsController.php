<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\QueueBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\QueueBundle\Entity\Job;
use Phlexible\Bundle\QueueBundle\Form\Type\JobType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Data controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Security("is_granted('ROLE_QUEUE')")
 * @Rest\NamePrefix("phlexible_api_queue_")
 */
class JobsController extends FOSRestController
{
    /**
     * Get jobs.
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Job",
     *   section="job",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getJobsAction()
    {
        $jobManager = $this->get('phlexible_queue.job_manager');

        $jobs = $jobManager->findBy(array(), array('createdAt' => 'DESC'));

        return array(
            'jobs' => $jobs,
            'count' => count($jobs),
        );
    }

    /**
     * Get job.
     *
     * @param string $jobId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Job",
     *   section="job",
     *   output="Phlexible\Bundle\QueueBundle\Entity\Job",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when job was not found"
     *   }
     * )
     */
    public function getJobAction($jobId)
    {
        $jobManager = $this->get('phlexible_queue.job_manager');

        $job = $jobManager->find($jobId);

        if (!$job instanceof Job) {
            throw new NotFoundHttpException('Job not found');
        }

        return array(
            'job' => $job,
        );
    }

    /**
     * Create job.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create a Job",
     *   section="job",
     *   input="Phlexible\Bundle\QueueBundle\Form\Type\JobType",
     *   statusCodes={
     *     201="Returned when job was created",
     *     204="Returned when job was updated",
     *     404="Returned when job was not found"
     *   }
     * )
     */
    public function postJobsAction(Request $request)
    {
        return $this->processForm($request, new Job(''));
    }

    /**
     * Update job.
     *
     * @param Request $request
     * @param string  $jobId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Update a Job",
     *   section="job",
     *   input="Phlexible\Bundle\QueueBundle\Form\Type\JobType",
     *   statusCodes={
     *     201="Returned when job was created",
     *     204="Returned when job was updated",
     *     404="Returned when job was not found"
     *   }
     * )
     */
    public function putMediatemplateAction(Request $request, $jobId)
    {
        $jobManager = $this->get('phlexible_queue.job_manager');
        $job = $jobManager->find($jobId);

        if (!$job instanceof Job) {
            throw new NotFoundHttpException('Job not found');
        }

        return $this->processForm($request, $job);
    }

    /**
     * @param Request $request
     * @param Job     $job
     *
     * @return Rest\View|Response
     */
    private function processForm(Request $request, Job $job)
    {
        $statusCode = !$job->getId() ? 201 : 204;

        $form = $this->createForm(new JobType(), $job);
        $form->submit($request);

        if ($form->isValid()) {
            $jobManager = $this->get('phlexible_queue.job_manager');
            $jobManager->updateTemplate($job);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set(
                    'Location',
                    $this->generateUrl('phlexible_api_queue_get_job', array('jobId' => $job->getId()), true)
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete job.
     *
     * @param string $jobId
     *
     * @return Response
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Delete a Job",
     *   section="job",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the job is not found"
     *   }
     * )
     */
    public function deleteJobAction($jobId)
    {
        $jobManager = $this->get('phlexible_queue.job_manager');
        $job = $jobManager->find($jobId);

        if (!$job instanceof Job) {
            throw new NotFoundHttpException('Job not found');
        }

        $jobManager->deleteJob($job);
    }
}
