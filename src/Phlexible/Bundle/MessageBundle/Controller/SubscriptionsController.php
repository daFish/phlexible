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
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\MessageBundle\Entity\Subscription;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Subscriptions controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Prefix("/message")
 * @NamePrefix("phlexible_message_")
 */
class SubscriptionsController extends FOSRestController
{
    /**
     * Get subscriptions
     *
     * @return Response
     *
     * @ApiDoc()
     */
    public function getSubscriptionsAction()
    {
        $subscriptionManager = $this->get('phlexible_message.subscription_manager');

        $subscriptions = $subscriptionManager->findAll();

        return $this->handleView($this->view($subscriptions));
    }

    /**
     * Create subscription
     *
     * @param Subscription $subscription
     *
     * @return Response
     * @ParamConverter("subscription", converter="fos_rest.request_body")
     * @Post("/subscriptions")
     * @ApiDoc()
     */
    public function postSubscriptionsAction(Subscription $subscription)
    {
        $subscriptionManager = $this->get('phlexible_message.subscription_manager');

        $subscriptionManager->updateSubscription($subscription);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }

    /**
     * Delete subscription
     *
     * @param string $subscriptionId
     *
     * @return Response
     * @Route("/delete/{subscriptionId}", name="messages_subscription_delete")
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
