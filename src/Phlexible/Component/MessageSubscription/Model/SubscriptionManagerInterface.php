<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MessageSubscription\Model;

use Phlexible\Component\MessageSubscription\Domain\Subscription;

/**
 * Subscription manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SubscriptionManagerInterface
{
    /**
     * @return \Phlexible\Component\MessageSubscription\Domain\Subscription
     */
    public function create();

    /**
     * @param string $id
     *
     * @return \Phlexible\Component\MessageSubscription\Domain\Subscription
     */
    public function find($id);

    /**
     * @return Subscription[]
     */
    public function findAll();

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return \Phlexible\Component\MessageSubscription\Domain\Subscription[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return Subscription
     */
    public function findOneBy(array $criteria, $orderBy = null);

    /**
     * @param \Phlexible\Component\MessageSubscription\Domain\Subscription $subscription
     */
    public function updateSubscription(Subscription $subscription);

    /**
     * @param Subscription $subscription
     */
    public function deleteSubscription(Subscription $subscription);
}
