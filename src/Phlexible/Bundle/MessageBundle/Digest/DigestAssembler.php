<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Digest;

use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Mailer\DigestMailer;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\MessageBundle\Model\FilterManagerInterface;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;
use Phlexible\Bundle\MessageBundle\Model\SubscriptionManagerInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Webmozart\Expression\Expr;

/**
 * Digest assembler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DigestAssembler
{
    /**
     * @var SubscriptionManagerInterface
     */
    private $subscriptionManager;

    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @param UserManagerInterface
     */
    private $userManager;

    /**
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param FilterManagerInterface       $filterManager
     * @param MessageManagerInterface      $messageManager
     * @param UserManagerInterface         $userManager
     */
    public function __construct(
        SubscriptionManagerInterface $subscriptionManager,
        FilterManagerInterface $filterManager,
        MessageManagerInterface $messageManager,
        UserManagerInterface $userManager
    ) {
        $this->subscriptionManager = $subscriptionManager;
        $this->filterManager = $filterManager;
        $this->messageManager = $messageManager;
        $this->userManager = $userManager;
    }

    /**
     * @return Digest[]
     */
    public function assembleDigests()
    {
        $subscriptions = $this->subscriptionManager->findBy(array('handler' => 'digest'));

        $digests = [];
        foreach ($subscriptions as $subscription) {
            $filter = $this->filterManager->find($subscription->getFilter()->getId());
            if (!$filter) {
                continue;
            }

            $user = $this->userManager->find($subscription->getUserId());
            if (!$user || !$user->getEmail()) {
                continue;
            }

            $lastSend = $subscription->getAttribute('lastSend', null);
            if (!$lastSend) {
                $lastSend = new \DateTime();
                $lastSend = $lastSend->sub(new \DateInterval('P30D'));
            } else {
                $lastSend = new \DateTime($lastSend);
            }

            $expression = Expr::greaterThan($lastSend->format('Y-m-d H:i:s'), 'createdAt')
                ->andX($filter->getExpression());

            $messages = $this->messageManager->findByExpression($expression);
            if (!count($messages)) {
                continue;
            }

            $digests[] = new Digest($user, $filter, $subscription, $lastSend, $messages);
        }

        return $digests;
    }
}
