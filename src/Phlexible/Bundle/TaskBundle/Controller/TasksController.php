<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Task\Type\TypeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Task controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Security("is_granted('ROLE_TASKS')")
 * @Prefix("/task")
 * @NamePrefix("phlexible_task_")
 */
class TasksController extends FOSRestController
{
    /**
     * Get tasks
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @ApiDoc(
     *   filters={
     *     {"name"="limit", "dataType"="integer", "default"=20, "description"="Limit results"},
     *     {"name"="start", "dataType"="integer", "default"=0, "description"="Result offset"},
     *     {"name"="sort", "dataType"="string", "default"="created_at", "description"="Sort field"},
     *     {"name"="dir", "dataType"="string", "default"="DESC", "description"="Sort direction"},
     *     {"name"="createdBy", "dataType"="string", "default"="involved", "description"="Created by"},
     *     {"name"="assignedTo", "dataType"="string", "default"="involved", "description"="Assigned to"},
     *     {"name"="involved", "dataType"="string", "default"="involved", "description"="Involved"},
     *     {"name"="status", "dataType"="string", "default"=false, "description"="Status"},
     *   }
     * )
     */
    public function getTasksAction(Request $request)
    {
        $type = $request->query->get('tasks', 'involved');
        $status = $request->query->get('status');
        $createdBy = $request->query->get('createdBy');
        $assignedTo = $request->query->get('assignedTo');
        $involved = $request->query->get('involved');
        $sort = $request->query->get('sort', 'createdAt');
        $dir = $request->query->get('dir', 'DESC');
        $limit = $request->query->get('limit', 20);
        $start = $request->query->get('start', 0);

        $taskManager = $this->get('phlexible_task.task_manager');
        $userManager = $this->get('phlexible_user.user_manager');
        $types = $this->get('phlexible_task.types');

        $userId = $this->getUser()->getId();

        $criteria = array();

        if ($status) {
            $criteria['status'] = $status;
        }
        if ($createdBy) {
            $criteria['createdBy'] = $createdBy;
        }
        if ($assignedTo) {
            $criteria['assignedTo'] = $assignedTo;
        }
        if ($involved) {
            $criteria['involved'] = $involved;
        }

        $tasks = $taskManager->findBy($criteria, [$sort => $dir], $limit, $start);
        $total = $taskManager->countBy($criteria);

        return $this->handleView($this->view(
            array(
                'tasks' => $tasks,
                'count' => $total
            )
        ));

        $data = [];
        foreach ($tasks as $task) {
            /* @var $task Task */
            $assignedUser = $userManager->find($task->getAssignedUserId());
            $createUser = $userManager->find($task->getCreateUserId());

            $transitions = [];
            foreach ($task->getTransitions() as $transition) {
                $transitionUser = $userManager->find($transition->getCreateUserId());
                $transitions[] = [
                    'id'          => $transition->getId(),
                    'name'        => $transition->getName(),
                    'new_state'   => $transition->getNewState(),
                    'old_state'   => $transition->getOldState(),
                    'create_date' => $transition->getCreatedAt()->format('Y-m-d H:i:s'),
                    'create_user' => $transitionUser->getDisplayName(),
                ];
            }

            $comments = [];
            foreach ($task->getComments() as $comment) {
                $commentUser = $userManager->find($comment->getCreateUserId());
                $comments[] = [
                    'id'            => $comment->getId(),
                    'current_state' => $comment->getCurrentState(),
                    'comment'       => $comment->getComment(),
                    'create_date'   => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                    'create_user'   => $commentUser->getDisplayName(),
                ];
            }

            $type = $types->get($task->getType());

            $data[] = [
                'id'             => $task->getId(),
                'type'           => $task->getType(),
                'generic'        => $task->getType() === 'generic',
                'title'          => $type->getTitle($task),
                'text'           => $type->getText($task),
                'description'    => $task->getDescription(),
                'component'      => $type->getComponent(),
                'link'           => $type->getLink($task),
                'assigned_user'  => $assignedUser->getDisplayName(),
                'status'         => $task->getFiniteState(),
                'create_user'    => $createUser->getDisplayName(),
                'create_uid'     => $task->getCreateUserId(),
                'create_date'    => $task->getCreatedAt()->format('Y-m-d H:i:s'),
                'transitions'    => $transitions,
                'comments'       => $comments,
                'states'         => $taskManager->getTransitions($task),
            ];
        }

        return $this->handleView($this->view(
            array(
                'tasks' => $tasks,
                'count' => $total
            )
        ));
    }

    /**
     * Get tasks
     *
     * @param Request $request
     * @param string  $taskId
     *
     * @return Response
     *
     * @View
     * @ApiDoc(
     *   filters={
     *     {"name"="limit", "dataType"="integer", "default"=20, "description"="Limit results"},
     *     {"name"="start", "dataType"="integer", "default"=0, "description"="Result offset"},
     *     {"name"="sort", "dataType"="string", "default"="created_at", "description"="Sort field"},
     *     {"name"="dir", "dataType"="string", "default"="DESC", "description"="Sort direction"},
     *     {"name"="tasks", "dataType"="string", "default"="involved", "description"="involvement"},
     *     {"name"="status_open", "dataType"="boolean", "default"=false, "description"="Status open"},
     *     {"name"="status_rejected", "dataType"="boolean", "default"=false, "description"="Status rejected"},
     *     {"name"="status_reopened", "dataType"="boolean", "default"=false, "description"="Status reopened"},
     *     {"name"="status_finished", "dataType"="boolean", "default"=false, "description"="Status finished"},
     *     {"name"="status_closed", "dataType"="boolean", "default"=false, "description"="Status closed"}
     *   }
     * )
     */
    public function getTaskAction(Request $request, $taskId)
    {
        $taskManager = $this->get('phlexible_task.task_manager');

        $task = $taskManager->find($taskId);

        return $task;
    }

    /**
     * List types
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/types", name="tasks_types")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="List task types",
     *   filters={
     *     {"name"="component", "dataType"="string", "description"="Component filter"}
     *   }
     * )
     */
    public function typesAction(Request $request)
    {
        $component = $request->request->get('component');

        $taskTypes = $this->get('phlexible_task.types');

        $types = [];
        foreach ($taskTypes->all() as $type) {
            /* @var $type TypeInterface */
            if ($component && $type->getComponent() !== $component) {
                continue;
            }

            $types[] = [
                'id'   => $type->getName(),
                'name' => $type->getName(),
            ];
        }

        return new JsonResponse(['types' => $types]);
    }

    /**
     * List recipients
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/recipients", name="tasks_recipients")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="List recipients",
     *   requirements={
     *     {"name"="type", "dataType"="string", "required"=true, "description"="Task type"},
     *   }
     * )
     */
    public function recipientsAction(Request $request)
    {
        $taskType = $request->get('type');

        $types = $this->get('phlexible_task.types');
        $userManager = $this->get('phlexible_user.user_manager');
        $authorizationChecker = $this->get('security.authorization_checker');

        $systemUserId = $userManager->getSystemUserId();

        $type = $types->get($taskType);

        $users = [];
        foreach ($userManager->findAll() as $user) {
            if ($user->getId() === $systemUserId) {
                continue;
            }

            if (!$authorizationChecker->isGranted('tasks')) {
                continue;
            }

            if ($type->getResource() && !$authorizationChecker->isGranted($type->getResource())) {
                continue;
            }

            $users[$user->getDisplayName()] = [
                'uid'      => $user->getId(),
                'username' => $user->getDisplayName(),
            ];
        }

        ksort($users);
        $users = array_values($users);

        return new JsonResponse(['users' => $users]);
    }

    /**
     * Create task
     *
     * @param Request $request
     *
     * @return Response
     * @ApiDoc(
     *   description="Create task",
     *   requirements={
     *     {"name"="type", "dataType"="string", "required"=true, "description"="Task type"},
     *     {"name"="recipient", "dataType"="string", "required"=true, "description"="Recipient"},
     *     {"name"="description", "dataType"="string", "required"=true, "description"="Description"},
     *     {"name"="payload", "dataType"="array", "required"=true, "description"="Payload"}
     *   }
     * )
     */
    public function postTasksAction(Request $request)
    {
        $typeName = $request->get('type');
        $assignedUserId = $request->get('recipient');
        $description = $request->get('description');
        $payload = $request->get('payload');

        if ($payload) {
            $payload = json_decode($payload, true);
        }

        $taskManager = $this->get('phlexible_task.task_manager');
        $userManager = $this->get('phlexible_user.user_manager');
        $types = $this->get('phlexible_task.types');

        $type = $types->get($typeName);
        $assignedUser = $userManager->find($assignedUserId);

        $task = $taskManager->createTask($type, $this->getUser(), $assignedUser, $payload, $description);

        return new ResultResponse(true, 'Task created.');
    }

    /**
     * Create task comment
     *
     * @param Request $request
     * @param string  $taskId
     *
     * @return Response
     * @ApiDoc(
     *   description="Create task comment",
     *   requirements={
     *     {"name"="comment", "dataType"="string", "required"=true, "description"="Comment"}
     *   }
     * )
     */
    public function postTaskCommentAction(Request $request, $taskId)
    {
        $id = $request->get('id');
        $comment = $request->get('comment');

        if ($comment) {
            $comment = urldecode($comment);
        }

        $taskManager = $this->get('phlexible_task.task_manager');

        $task = $taskManager->find($id);
        $taskManager->updateTask($task, $this->getUser(), $comment, null, $comment);

        return new ResultResponse(true, 'Task comment created.');
    }

    /**
     * Create task transition
     *
     * @param Request $request
     * @param string  $taskId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create task transition",
     *   requirements={
     *     {"name"="recipient", "dataType"="string", "required"=false, "description"="Recipient"},
     *     {"name"="name", "dataType"="string", "required"=true, "description"="Transition name"},
     *     {"name"="comment", "dataType"="string", "required"=false, "description"="Comment"}
     *   }
     * )
     */
    public function postTaskTransitionAction(Request $request, $taskId)
    {
        $assignedUserId = $request->get('recipient');
        $name = $request->get('name');
        $comment = $request->get('comment');

        if ($comment) {
            $comment = urldecode($comment);
        }

        $taskManager = $this->get('phlexible_task.task_manager');
        $userManager = $this->get('phlexible_user.user_manager');

        $assignUser = null;
        if ($assignedUserId) {
            $assignUser = $userManager->find($assignedUserId);
        }

        $task = $taskManager->find($id);
        $taskManager->updateTask($task, $this->getUser(), $name, $assignUser, $comment);

        return new ResultResponse(true, 'Task transition created.');
    }

    /**
     * Assign task
     *
     * @param Request $request
     * @param string  $taskId
     *
     * @return Response
     * @ApiDoc(
     *   description="Create status",
     *   requirements={
     *     {"name"="id", "dataType"="string", "required"=true, "description"="Task ID"},
     *     {"name"="recipient", "dataType"="string", "required"=true, "description"="Recipient"},
     *     {"name"="comment", "dataType"="string", "required"=false, "description"="Comment"}
     *   }
     * )
     */
    public function assignTaskAction(Request $request, $taskId)
    {
        $assignedUserId = $request->get('recipient');
        $comment = $request->get('comment');

        if ($comment) {
            $comment = urldecode($comment);
        }

        $taskManager = $this->get('phlexible_task.task_manager');
        $userManager = $this->get('phlexible_user.user_manager');

        $task = $taskManager->find($taskId);
        $assignUser = $userManager->find($assignedUserId);

        $taskManager->updateTask($task, $this->getUser(), null, $assignUser, $comment);

        return new ResultResponse(true, 'Task assigned.');
    }

    /**
     * View task
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/view", name="tasks_view")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="View",
     *   requirements={
     *     {"name"="id", "dataType"="string", "required"=true, "description"="Task ID"}
     *   }
     * )
     */
    public function viewAction(Request $request)
    {
        $id = $request->get('id');

        $taskManager = $this->get('phlexible_task.task_manager');
        $types = $this->get('phlexible_task.types');
        $userManager = $this->get('phlexible_user.user_manager');

        $task = $taskManager->find($id);

        $createUser = $userManager->find($task->getCreateUserId());
        $assignedUser = $userManager->find($task->getAssignedUserId());

        $transitions = [];
        foreach ($task->getTransitions() as $transition) {
            $transitionUser = $userManager->find($transition->getCreateUserId());
            $history[] = [
                'create_date' => $transition->getCreatedAt()->format('Y-m-d H:i:s'),
                'name'        => $transitionUser->getDisplayName(),
                'status'      => $transition->getNewState(),
                'latest'      => 1,
            ];
        }
        $transitions = array_reverse($transitions);

        $comments = [];
        foreach ($task->getComments() as $comment) {
            $commentUser = $userManager->find($comment->getCreateUserId());
            $history[] = [
                'create_date' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'name'        => $commentUser->getDisplayName(),
                'status'      => $comment->getCurrentState(),
                'comment'     => $comment->getComment(),
                'latest'      => 1,
            ];
        }

        $type = $types->get($task->getType());

        $data = [
            'id'             => $task->getId(),
            'type'           => $task->getType(),
            'title'          => $type->getTitle($task),
            'text'           => $type->getText($task),
            'component'      => $type->getComponent(),
            'created'        => $task->getCreateUserId() === $this->getUser()->getId() ? 1 : 0,
            'assigned'       => $task->getAssignedUserId() === $this->getUser()->getId() ? 1 : 0,
            'assigned_user'  => $assignedUser->getDisplayName(),
            'assigned_uid'   => $task->getAssignedUserId(),
            'create_user'    => $createUser->getDisplayName(),
            'create_uid'     => $task->getCreateUserId(),
            'create_date'    => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            'latest_status'  => $task->getFiniteState(),
            'latest_comment' => '',//$latestStatus->getComment(),
            'latest_user'    => '',//$assignedUser->getDisplayName(),
            'latest_uid'     => '',//$latestStatus->getCreateUserId(),
            'latest_date'    => '',//$latestStatus->getCreatedAt()->format('Y-m-d H:i:s'),
            //'recipient_uid'  => $task->getRecipientUserId(),
            //'latest_id'      => $latestStatus->getId(),
            'transitions'    => $transitions,
            'comments'       => $comments,
        ];

        return new JsonResponse($data);
    }
}
