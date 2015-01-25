<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Criteria\Criterium;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Filter controller
 *
 * @author Caspar Baratella <cb@brainbits.net>
 * @Route("/messages/filters")
 */
class FiltersController extends Controller
{
    /**
     * List filters
     *
     * @return JsonResponse
     * @Route("", name="messages_filters")
     */
    public function listAction()
    {
        $filterManager = $this->get('phlexible_message.filter_manager');

        $filters = [];

        foreach ($filterManager->findBy(['userId' => $this->getUser()->getId()]) as $filter) {
            $criteria = [];
            foreach ($filter->getCriteria() as $groupIndex => $group) {
                foreach ($group as $criterium) {
                    $criteria[] = [
                        'criteria' => $criterium->getType(),
                        'value'    => $criterium->getValue(),
                        'group'    => $groupIndex + 1,
                    ];
                }
            }

            $filters[] = [
                'id'       => $filter->getId(),
                'title'    => $filter->getTitle(),
                'criteria' => $criteria,
            ];
        }

        return new JsonResponse($filters);
    }

    /**
     * List filter values
     *
     * @return JsonResponse
     * @Route("/facets", name="messages_filter_facets")
     */
    public function facetsAction()
    {
        $messageManager = $this->get('phlexible_message.message_manager');
        $data = $messageManager->getFacets();

        $data['priorityNames'] = $messageManager->getPriorityNames();
        $data['typeNames'] = $messageManager->getTypeNames();

        return new JsonResponse($data);
    }

    /**
     * Create filter
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="messages_filter_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title');

        $filterManager = $this->get('phlexible_message.filter_manager');

        $filter = $filterManager->create();
        $filter
            ->setUserId($this->getUser()->getId())
            ->setTitle($title)
            ->setModifiedAt(new \DateTime())
            ->setCreatedAt(new \DateTime());

        $filterManager->updateFilter($filter);

        return new ResultResponse(true, 'Filter created.');
    }

    /**
     * Updates a Filter
     *
     * @param Request $request
     * @param string  $id
     *
     * @return ResultResponse
     * @Route("/update/{id}", name="messages_filter_update")
     */
    public function updateAction(Request $request, $id)
    {
        $title = $request->get('title');
        $rawCriteria = json_decode($request->get('criteria'), true);

        $filterManager = $this->get('phlexible_message.filter_manager');

        $filter = $filterManager->find($id);
        $filter->setTitle($title);

        $criteria = new Criteria();
        $criteria->setMode(Criteria::MODE_OR);
        foreach ($rawCriteria as $group) {
            $criteriaGroup = new Criteria();
            $criteriaGroup->setMode(Criteria::MODE_AND);
            foreach ($group as $row) {
                if (!strlen($row['value'])) {
                    continue;
                }

                $criterium = new Criterium($row['key'], $row['value']);
                $criteriaGroup->add($criterium);
            }
            if ($criteriaGroup->count()) {
                $criteria->addCriteria($criteriaGroup);
            }
        }
        $filter->setCriteria($criteria);

        $filterManager->updateFilter($filter);

        return new ResultResponse(true, 'Filter updated');
    }

    /**
     * Delete filter
     *
     * @param string $id
     *
     * @return ResultResponse
     * @Route("/delete/{id}", name="messages_filter_delete")
     */
    public function deleteAction($id)
    {
        $filterManager = $this->get('phlexible_message.filter_manager');
        $filter = $filterManager->find($id);
        $filterManager->deleteFilter($filter);

        return new ResultResponse(true, 'Filter "' . $filter->getTitle() . '" deleted.');
    }

    /**
     * Preview messages
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/preview", name="messages_filter_preview")
     */
    public function previewAction(Request $request)
    {
        $rawCriteria = json_decode($request->get('filters'), true);

        $messageManager = $this->get('phlexible_message.message_manager');

        $criteria = new Criteria();
        $criteria->setMode(Criteria::MODE_OR);
        foreach ($rawCriteria as $group) {
            $criteriaGroup = new Criteria();
            $criteriaGroup->setMode(Criteria::MODE_AND);
            foreach ($group as $row) {
                if (!strlen($row['value'])) {
                    continue;
                }

                $criterium = new Criterium($row['key'], $row['value']);
                $criteriaGroup->add($criterium);
            }
            if ($criteriaGroup->count()) {
                $criteria->addCriteria($criteriaGroup);
            }
        }

        if (!$criteria->count()) {
            return new JsonResponse([
                'total'    => 0,
                'messages' => []
            ]);
        }

        $messages = $messageManager->findByCriteria($criteria, ['createdAt' => 'DESC'], 20);
        $count = $messageManager->countByCriteria($criteria);

        $priorityList = $messageManager->getPriorityNames();
        $typeList = $messageManager->getTypeNames();

        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'subject'    => $message->getSubject(),
                'body'       => nl2br($message->getBody()),
                'priority'   => $priorityList[$message->getPriority()],
                'type'       => $typeList[$message->getType()],
                'channel'    => $message->getChannel(),
                'role'       => $message->getRole(),
                'created_at' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'user'       => $message->getUser(),
            ];
        }

        return new JsonResponse([
            'total'    => $count,
            'messages' => $data
        ]);
    }
}
