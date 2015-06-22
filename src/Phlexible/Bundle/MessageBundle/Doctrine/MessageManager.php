<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Entity\Repository\MessageRepository;
use Phlexible\Bundle\MessageBundle\Exception\LogicException;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;
use Webmozart\Expression\Expr;
use Webmozart\Expression\Expression;

/**
 * Doctrine message manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageManager implements MessageManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return MessageRepository
     */
    private function getMessageRepository()
    {
        if (null === $this->messageRepository) {
            $this->messageRepository = $this->entityManager->getRepository('PhlexibleMessageBundle:Message');
        }

        return $this->messageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getMessageRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getMessageRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $orderBy = null)
    {
        return $this->getMessageRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function expr()
    {
        return Expr::true();
    }

    /**
     * {@inheritdoc}
     */
    public function findByExpression(Expression $expression, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getMessageRepository()->findByExpression($expression, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function countByExpression(Expression $expression)
    {
        return $this->getMessageRepository()->countByExpression($expression);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByExpression(Expression $expression, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getMessageRepository()->findOneByExpression($expression, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        return $this->getMessageRepository()->getFacets();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetsByExpression(Expression $expression)
    {
        return $this->getMessageRepository()->getFacetsByExpression($expression);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeNames()
    {
        return [
            0 => 'info',
            1 => 'error',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateMessage(Message $message)
    {
        if (!$this->entityManager->isOpen()) {
            return;
        }

        if ($message->getId()) {
            throw new LogicException('Messages can\'t be updated.');
        }

        if (!$message->getUser()) {
            if (PHP_SAPI === 'cli') {
                $user = 'cli';
            } else {
                $user = 'unknown';
            }

            $rc = new \ReflectionClass('Phlexible\Bundle\MessageBundle\Entity\Message');
            $rp = $rc->getProperty('user');
            $rp->setAccessible(true);
            $rp->setValue($message, $user);
        }

        $this->entityManager->persist($message);
        $this->entityManager->flush($message);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMessage(Message $message)
    {
        $this->entityManager->remove($message);
        $this->entityManager->flush();
    }
}
