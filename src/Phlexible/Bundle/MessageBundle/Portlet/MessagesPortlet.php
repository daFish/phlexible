<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Domain\Portlet;
use Phlexible\Component\Message\Model\MessageManagerInterface;
use Phlexible\Component\MessageSubscription\Model\SubscriptionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Messages portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessagesPortlet extends Portlet
{
    /**
     * @var \Phlexible\Component\MessageSubscription\Model\SubscriptionManagerInterface
     */
    private $subscriptionManager;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param \Phlexible\Component\MessageSubscription\Model\SubscriptionManagerInterface $subscriptionManager
     * @param MessageManagerInterface      $messageManager
     * @param TokenStorageInterface        $tokenStorage
     */
    public function __construct(
        SubscriptionManagerInterface $subscriptionManager,
        MessageManagerInterface $messageManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->subscriptionManager = $subscriptionManager;
        $this->messageManager = $messageManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        /*
        $subscription = $this->subscriptionManager
            ->findOneBy(
                array('userId' => $this->tokenStorage->getToken()->getUser()->getId(), 'handler' => 'portlet')
            );

        if (!$subscription) {
            return array();
        }

        $filter = $subscription->getFilter();

        if (!$filter) {
            return array();
        }

        $messages = $this->messageManager->findByExpression($filter->getExpression(), ['createdAt' => 'DESC'], 20);
        */
        $messages = $this->messageManager->findBy(array(), array('createdAt' => 'DESC'), 20);

        $data = array();
        foreach ($messages as $message) {
            $data[] = array(
                'id'        => $message->getId(),
                'subject'   => $message->getSubject(),
                'body'      => $message->getBody(),
                'type'      => $message->getType(),
                'channel'   => $message->getChannel(),
                'role'      => $message->getRole(),
                'user'      => $message->getUser(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            );
        }

        return $data;
    }
}
