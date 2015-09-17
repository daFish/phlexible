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
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\MessageBundle\Form\Type\SubscriptionType;
use Phlexible\Component\MessageSubscription\Domain\Subscription;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Subscriptions controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MESSAGE_SUBSCRIPTIONS')")
 * @Rest\NamePrefix("phlexible_api_message_")
 */
class SubscriptionsController extends FOSRestController
{
    /**
     * Get subscriptions.
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Subscription",
     *   section="message",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getSubscriptionsAction()
    {
        $subscriptionManager = $this->get('phlexible_message.subscription_manager');
        $subscriptions = $subscriptionManager->findAll();

        return array(
            'subscriptions' => $subscriptions,
            'count' => count($subscriptions),
        );
    }

    /**
     * Get subscription.
     *
     * @param string $subscriptionId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Subscription",
     *   section="message",
     *   output="Phlexible\Bundle\MessageBundle\Entity\Subscription",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when subscription was not found"
     *   }
     * )
     */
    public function getSubscriptionAction($subscriptionId)
    {
        $subscriptionManager = $this->get('phlexible_message.subscription_manager');
        $subscription = $subscriptionManager->find($subscriptionId);

        if (!$subscription instanceof Subscription) {
            throw new NotFoundHttpException('Subscription not found');
        }

        return array(
            'subscription' => $subscription,
        );
    }

    /**
     * Create subscription.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create a Subscription",
     *   section="message",
     *   input="Phlexible\Bundle\MessageBundle\Form\Type\SubscriptionType",
     *   statusCodes={
     *     201="Returned when subscription was created",
     *     204="Returned when subscription was updated",
     *     404="Returned when subscription was not found"
     *   }
     * )
     */
    public function postSubscriptionsAction(Request $request)
    {
        return $this->processForm($request, new Subscription());
    }

    /**
     * Update subscription.
     *
     * @param Request $request
     * @param string  $subscriptionId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Update a Subscription",
     *   section="message",
     *   input="Phlexible\Bundle\MessageBundle\Form\Type\SubscriptionType",
     *   statusCodes={
     *     201="Returned when subscription was created",
     *     204="Returned when subscription was updated",
     *     404="Returned when subscription was not found"
     *   }
     * )
     */
    public function putSubscriptionAction(Request $request, $subscriptionId)
    {
        $subscriptionManager = $this->get('phlexible_message.subscription_manager');
        $subscription = $subscriptionManager->find($subscriptionId);

        if (!$subscription instanceof Subscription) {
            throw new NotFoundHttpException('Subscription not found');
        }

        return $this->processForm($request, $subscription);
    }

    /**
     * @param Request                                                      $request
     * @param \Phlexible\Component\MessageSubscription\Domain\Subscription $subscription
     *
     * @return Rest\View|Response
     */
    private function processForm(Request $request, Subscription $subscription)
    {
        $statusCode = !$subscription->getId() ? 201 : 204;

        $form = $this->createForm(new SubscriptionType(), $subscription);
        $form->submit($request);

        if ($form->isValid()) {
            $subscriptionManager = $this->get('phlexible_message.subscription_manager');
            $subscriptionManager->updateSiteroot($subscription);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set(
                    'Location',
                    $this->generateUrl(
                        'phlexible_api_message_get_subscription',
                        array('siterootId' => $subscription->getId()),
                        true
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete subscription.
     *
     * @param string $subscriptionId
     *
     * @return Response
     *
     * @Route("/delete/{subscriptionId}", name="messages_subscription_delete")
     * @ApiDoc(
     *   description="Delete a Subscription",
     *   section="message",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the subscription is not found"
     *   }
     * )
     */
    public function deleteSubscriptionAction($subscriptionId)
    {
        $subscriptionManager = $this->get('phlexible_message.subscription_manager');

        $subscription = $subscriptionManager->find($subscriptionId);
        $subscriptionManager->deleteSubscription($subscription);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }
}
