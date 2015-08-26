<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Model\TaskManagerInterface;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineContextEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineEvent;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Phlexible\Component\Node\Event\NodeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Task listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TaskListener implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var TaskManagerInterface
     */
    private $taskManager;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param TaskManagerInterface  $taskManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, TaskManagerInterface $taskManager = null)
    {
        $this->taskManager = $taskManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TreeEvents::PUBLISH_NODE_CONTEXT     => 'onPublishNode',
            TreeEvents::SET_NODE_OFFLINE_CONTEXT => 'onSetNodeOffline',
            TreeEvents::DELETE_NODE              => 'onDeleteNode',
        );
    }

    /**
     * @param PublishNodeContextEvent $event
     */
    public function onPublishNode(PublishNodeContextEvent $event)
    {
        if (!$this->taskManager) {
            return;
        }

        $node = $event->getNode();
        $language = $event->getLanguage();

        if ($node->getContentType() !== 'element') {
            return;
        }

        $this->doTask(
            array(
                'type' => 'element',
                'type_id' => $node->getId(),
                'language' => $language
            ),
            'element.publish',
            $this->tokenStorage->getToken()->getUser()->getId()
        );
    }

    /**
     * @param SetNodeOfflineContextEvent $event
     */
    public function onSetNodeOffline(SetNodeOfflineContextEvent $event)
    {
        if (!$this->taskManager) {
            return;
        }

        $node = $event->getNode();
        $language = $event->getLanguage();

        if ($node->getContentType() !== 'element') {
            return;
        }

        $this->doTask(
            array(
                'type' => 'element',
                'type_id' => $node->getId(),
                'language' => $language
            ),
            'element.set_offline',
            $this->tokenStorage->getToken()->getUser()->getId()
        );
    }

    /**
     * @param \Phlexible\Component\Node\Event\NodeEvent $event
     */
    public function onDeleteNode(NodeEvent $event)
    {
        if (!$this->taskManager) {
            return;
        }

        $node = $event->getNode();
        $language = null;

        if ($node->getContentType() !== 'element') {
            return;
        }

        $this->doTask(
            array(
                'type' => 'element',
                'type_id' => $node->getId()
            ),
            'element.delete',
            $this->tokenStorage->getToken()->getUser()->getId()
        );
    }

    /**
     * @param array  $payload
     * @param string $type
     * @param string $userId
     */
    private function doTask(array $payload, $type, $userId)
    {
        $tasks = $this->taskManager->findBy(
            array(
                'type' => $type,
                'finiteState' => array(
                    Task::STATUS_OPEN,
                    Task::STATUS_REJECTED,
                    Task::STATUS_REOPENED,
                )
            )
        );

        if (!$tasks) {
            return;
        }

        ksort($payload);

        foreach ($tasks as $task) {
            /* @var $task Task */
            $taskPayload = $task->getPayload();
            ksort($taskPayload);

            if ($payload != $taskPayload) {
                continue;
            }

            $this->taskManager->createStatus($task, $userId, 'task done', Task::STATUS_FINISHED);
        }
    }
}
