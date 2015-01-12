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
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param TranslatorInterface          $translator
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param MessageManagerInterface      $messageManager
     * @param SecurityContextInterface     $securityContext
     */
    public function __construct(
        TranslatorInterface $translator,
        SubscriptionManagerInterface $subscriptionManager,
        MessageManagerInterface $messageManager,
        SecurityContextInterface $securityContext)
    {
        $this
            ->setId('messages-portlet')
            ->setTitle($translator->trans('messages.messages', [], 'gui'))
            ->setXtype('messages-portlet')
            ->setIconClass('p-message-component-icon')
            ->setRole('ROLE_MESSAGE_SUBSCRIPTIONS');

        $this->subscriptionManager = $subscriptionManager;
        $this->messageManager = $messageManager;
        $this->securityContext = $securityContext;
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
                ['userId' => $this->securityContext->getToken()->getUser()->getId(), 'handler' => 'portlet']
            );

        if (!$subscription) {
            return [];
        }

        $filter = $subscription->getFilter();

        if (!$filter) {
            return [];
        }

        $messages = $this->messageManager->findByCriteria($filter->getCriteria(), ['createdAt' => 'DESC'], 20);

        $priorityList = $this->messageManager->getPriorityNames();
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
                'priority' => $priorityList[$message->getPriority()],
                'type'     => $typeList[$message->getType()],
                'channel'  => $message->getChannel(),
            ];
        }

        return $data;
    }
}
