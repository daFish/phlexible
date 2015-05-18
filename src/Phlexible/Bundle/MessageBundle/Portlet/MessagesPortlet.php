<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;
use Phlexible\Bundle\MessageBundle\Model\SubscriptionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Messages portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessagesPortlet extends Portlet
{
    /**
     * @var SubscriptionManagerInterface
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
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param MessageManagerInterface      $messageManager
     * @param TokenStorageInterface        $tokenStorage
     */
    public function __construct(
        SubscriptionManagerInterface $subscriptionManager,
        MessageManagerInterface $messageManager,
        TokenStorageInterface $tokenStorage)
    {
        $this
            ->setId('messages-portlet')
            ->setXtype('messages-portlet')
            ->setIconClass('resource-monitor')
            ->setRole('ROLE_MESSAGE_SUBSCRIPTIONS');

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
        $subscription = $this->subscriptionManager
            ->findOneBy(
                ['userId' => $this->tokenStorage->getToken()->getUser()->getId(), 'handler' => 'portlet']
            );

        if (!$subscription) {
            return [];
        }

        $filter = $subscription->getFilter();

        if (!$filter) {
            return [];
        }

        $messages = $this->messageManager->findByExpression($filter->getExpression(), ['createdAt' => 'DESC'], 20);

        $typeList = $this->messageManager->getTypeNames();

        $data = [];
        foreach ($messages as $message) {
            $subject = '';

            $i = 0;
            do {
                $subject .= ($i ? '<wbr />' : '') . substr($message->getSubject(), $i, $i + 30);
                $i += 30;
            } while ($i <= strlen($message->getSubject()));

            $data[] = [
                'id'       => $message->getId(),
                'subject'  => $subject,
                'time'     => $message->getCreatedAt()->format('U'),
                'type'     => $typeList[$message->getType()],
                'channel'  => $message->getChannel(),
            ];
        }

        return $data;
    }
}
