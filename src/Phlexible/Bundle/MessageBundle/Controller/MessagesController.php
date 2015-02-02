<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Messages controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Prefix("/message")
 * @NamePrefix("phlexible_message_")
 */
class MessagesController extends FOSRestController
{
    /**
     * Get messages
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     *
     * @QueryParam(name="start", requirements="\d+", default=0, description="First result")
     * @QueryParam(name="limit", requirements="\d+", default=20, description="Max results")
     * @QueryParam(name="sort", requirements="\w+", default="createdAt", description="Sort field")
     * @QueryParam(name="dir", requirements="\w+", default="DESC", description="Sort direction")
     * @QueryParam(name="include", requirements="\w+", default="facets", description="Include optional values, like facets")
     * @QueryParam(name="criteria", description="Search criteria.")
     * @ApiDoc
     */
    public function getMessagesAction(ParamFetcher $paramFetcher)
    {
        $start = $paramFetcher->get('start');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');
        $dir = $paramFetcher->get('dir');
        $criteriaString = $paramFetcher->get('criteria');

        $messageManager = $this->get('phlexible_message.message_manager');
        $criteria = $messageManager->createCriteria();

        $criteria
            ->orderBy(array($sort => $dir))
            ->setFirstResult($start)
            ->setMaxResults($limit);

        //MessageCriteriaBuilder::applyFromRequest($criteria, $criteriaString);

        $messageResult = $messageManager->query($criteria);

        $messages = array();
        foreach ($messageResult as $message) {
            $messages[] = $message;
        }

        $data = array(
            'messages' => $messages,
            'count'    => count($messages),
            'facets'   => $messageManager->getFacets($criteria),
        );

        return $this->handleView($this->view($data));

        /*
        $priorityFilter = [];
        $typeFilter = [];
        $channelFilter = [];
        $roleFilter = [];

        foreach ($filter as $key => $value) {
            if ($key == 'subject' && !empty($value)) {
                $criteria->addRaw(Criteria::CRITERIUM_SUBJECT_LIKE, $value);
            } elseif ($key == 'text' && !empty($value)) {
                $criteria->addRaw(Criteria::CRITERIUM_BODY_LIKE, $value);
            } elseif (substr($key, 0, 9) == 'priority_') {
                $priorityFilter[] = substr($key, 9);
            } elseif (substr($key, 0, 5) == 'type_') {
                $typeFilter[] = substr($key, 5);
            } elseif (substr($key, 0, 8) == 'channel_') {
                $channelFilter[] = substr($key, 8);
            } elseif (substr($key, 0, 5) == 'role_') {
                $roleFilter[] = substr($key, 9);
            } elseif ($key == 'date_after' && !empty($value)) {
                $criteria->addRaw(Criteria::CRITERIUM_START_DATE, $value);
            } elseif ($key == 'date_before' && !empty($value)) {
                $criteria->addRaw(Criteria::CRITERIUM_END_DATE, $value);
            }
        }

        if (count($priorityFilter)) {
            $criteria->addRaw(
                Criteria::CRITERIUM_PRIORITY_IN,
                implode(',', $priorityFilter)
            );
        }

        if (count($typeFilter)) {
            $criteria->addRaw(
                Criteria::CRITERIUM_TYPE_IN,
                implode(',', $typeFilter)
            );
        }

        if (count($channelFilter)) {
            $criteria->addRaw(
                Criteria::CRITERIUM_CHANNEL_IN,
                implode(',', $channelFilter)
            );
        }

        if (count($roleFilter)) {
            $criteria->addRaw(
                Criteria::CRITERIUM_ROLE_IN,
                implode(',', $roleFilter)
            );
        }
        */
    }

    /**
     * Create message
     *
     * @param Message $message
     *
     * @return Response
     *
     * @ParamConverter("message", converter="fos_rest.request_body")
     * @Post("/messages")
     * @ApiDoc
     */
    public function postMessagesAction(Message $message)
    {
        $messageManager = $this->get('phlexible_message.message_manager');

        $messageManager->updateMessage($message);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }
}
