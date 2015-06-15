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
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineEvent;
use Phlexible\Bundle\TreeBundle\TreeEvents;
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
        return [
            TreeEvents::PUBLISH_NODE => 'onPublishNode',
            TreeEvents::SET_NODE_OFFLINE => 'onSetNodeOffline',
            TreeEvents::DELETE_NODE => 'onDeleteNode',
        ];
    }

    /**
     * @param PublishNodeEvent $event
     */
    public function onPublishNode(PublishNodeEvent $event)
    {
        if (!$this->taskManager) {
            return;
        }

        $node = $event->getNode();
        $language = $event->getLanguage();

        if ($node->getType() !== 'element') {
            return;
        }

        $this->doTask(
            [
                'type' => 'element',
                'type_id' => $node->getId(),
                'language' => $language
            ],
            'element.publish',
            $this->tokenStorage->getToken()->getUser()->getId()
        );
    }

    /**
     * @param SetNodeOfflineEvent $event
     */
    public function onSetNodeOffline(SetNodeOfflineEvent $event)
    {
        if (!$this->taskManager) {
            return;
        }

        $node = $event->getNode();
        $language = $event->getLanguage();

        if ($node->getType() !== 'element') {
            return;
        }

        $this->doTask(
            [
                'type' => 'element',
                'type_id' => $node->getId(),
                'language' => $language
            ],
            'element.set_offline',
            $this->tokenStorage->getToken()->getUser()->getId()
        );
    }

    /**
     * @param NodeEvent $event
     */
    public function onDeleteNode(NodeEvent $event)
    {
        if (!$this->taskManager) {
            return;
        }

        $node = $event->getNode();
        $language = null;

        if ($node->getType() !== 'element') {
            return;
        }

        $this->doTask(
            [
                'type' => 'element',
                'type_id' => $node->getId()
            ],
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
            [
                'type' => $type,
                'finiteState' => [
                    Task::STATUS_OPEN,
                    Task::STATUS_REJECTED,
                    Task::STATUS_REOPENED,
                ]
            ]
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
