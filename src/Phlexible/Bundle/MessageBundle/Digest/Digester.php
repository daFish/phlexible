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

/**
 * Digester
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Digester
{
    /**
     * @var DigestAssembler
     */
    private $digestAssembler;

    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @var DigestMailer
     */
    private $mailer;

    /**
     * @param DigestAssembler $digestAssembler
     * @param MessagePoster   $messagePoster
     * @param DigestMailer    $mailer
     */
    public function __construct(DigestAssembler $digestAssembler, MessagePoster $messagePoster, DigestMailer $mailer)
    {
        $this->digestAssembler = $digestAssembler;
        $this->messagePoster = $messagePoster;
        $this->mailer = $mailer;
    }

    /**
     * Static send function for use with events
     *
     * @return array
     */
    public function sendDigestMails()
    {
        $digests = $this->digestAssembler->assembleDigests();

        foreach ($digests as $digest) {
            if ($this->mailer->sendDigestMail($digest)) {
                //$digest->getSubscription()->setAttribute('lastSend', date('Y-m-d H:i:s'));
                //$this->subscriptionManager->updateSubscription($subscription);
            }
        }

        if (count($digests)) {
            $message = Message::create(
                count($digests) . ' digest mail(s) sent.',
                'Status: ' . PHP_EOL . print_r($digests, true),
                null,
                null,
                'ROLE_MESSAGES',
                'cli'
            );
            $this->messagePoster->post($message);
        }

        return $digests;
    }
}
