<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Form\Type\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Messages controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MESSAGES')")
 * @Rest\NamePrefix("phlexible_api_message_")
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
     * @Rest\QueryParam(name="start", requirements="\d+", default=0, description="First result")
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Max results")
     * @Rest\QueryParam(name="sort", requirements="\w+", default="createdAt", description="Sort field")
     * @Rest\QueryParam(name="dir", requirements="\w+", default="DESC", description="Sort direction")
     * @Rest\QueryParam(name="include", requirements="\w+", default="facets", description="Include optional values, like facets")
     * @Rest\QueryParam(name="criteria", description="Search criteria.")
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Message",
     *   section="message",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
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

        /* @var $criteria Criteria */
        $criteria
            ->orderBy(array($sort => $dir))
            ->setFirstResult($start)
            ->setMaxResults($limit);

        if ($criteriaString) {
            $criteriaArray = json_decode($criteriaString, true);
            foreach ($criteriaArray['value'] as $values) {
                switch ($values['op']) {
                    case 'in':
                        $x = $criteria->expr()->in($values['field'], $values['values']);
                        break;
                    case 'like':
                        $x = $criteria->expr()->contains($values['field'], $values['value']);
                        break;
                    default:
                        continue;
                }

                if ($criteriaArray['mode'] === 'AND') {
                    $criteria->andWhere($x);
                } else {
                    $criteria->orWhere($x);
                }
            }
        }

        //MessageCriteriaBuilder::applyFromRequest($criteria, $criteriaString);

        $messageResult = $messageManager->query($criteria);

        $messages = array();
        foreach ($messageResult as $message) {
            $messages[] = $message;
        }

        return array(
            'messages' => $messages,
            'count'    => count($messageResult),
            'facets'   => $messageManager->getFacets($criteria),
        );
    }

    /**
     * Get message
     *
     * @param string $messageId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Message",
     *   section="message",
     *   output="Phlexible\Bundle\MessageBundle\Entity\Message",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when message was not found"
     *   }
     * )
     */
    public function getMessageAction($messageId)
    {
        $messageManager = $this->get('phlexible_message.message_manager');
        $message = $messageManager->find($messageId);

        if (!$message instanceof Message) {
            throw new NotFoundHttpException('Message not found');
        }

        return array(
            'message' => $message
        );
    }

    /**
     * Create message
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ParamConverter("message", converter="fos_rest.request_body")
     * @Rest\Post("/messages")
     * @ApiDoc(
     *   description="Create a Message",
     *   section="message",
     *   input="Phlexible\Bundle\MessageBundle\Form\Type\MessageType",
     *   statusCodes={
     *     201="Returned when message was created",
     *     404="Returned when message was not found"
     *   }
     * )
     */
    public function postMessagesAction(Request $request)
    {
        return $this->processForm($request);
    }

    /**
     * @param Request $request
     * @param Message $message
     *
     * @return Rest\View|Response
     */
    private function processForm(Request $request, Message $message)
    {
        $statusCode = 201;

        $form = $this->createForm(new MessageType(), $message);
        $form->submit($request);

        if ($form->isValid()) {
            $messageManager = $this->get('phlexible_siteroot.message_manager');
            $messageManager->updateSiteroot($message);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'phlexible_api_message_get_message', array('siterootId' => $message->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete message
     *
     * @param string $messageId
     *
     * @return Response
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Delete a Message",
     *   section="message",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the siteroot is not found"
     *   }
     * )
     */
    public function deleteMessageAction($messageId)
    {
        $messageManager = $this->get('phlexible_siteroot.message_manager');
        $message = $messageManager->find($messageId);

        if (!$message instanceof Message) {
            throw new NotFoundHttpException('Message not found');
        }

        $messageManager->deleteSiteroot($message);
    }
}
