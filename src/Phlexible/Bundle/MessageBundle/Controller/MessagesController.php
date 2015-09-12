<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\MessageBundle\Form\Type\MessageType;
use Phlexible\Component\Expression\Serializer\ArrayExpressionSerializerInterface;
use Phlexible\Component\Message\Domain\Message;
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
     * @Rest\QueryParam(name="expression", description="Search expression.")
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
        $expression = $paramFetcher->get('expression');

        $messageManager = $this->get('phlexible_message.message_manager');
        $expr = $messageManager->expr();

        if ($expression) {
            $expression = json_decode($expression, true);
            $serializer = new ArrayExpressionSerializerInterface();
            $expr = $serializer->deserialize($expression);
        }

        return array(
            'expr'     => (string) $expr,
            'messages' => $messageManager->findByExpression($expr, array($sort => $dir), $limit, $start),
            'count'    => $messageManager->countByExpression($expr),
            'facets'   => $messageManager->getFacetsByExpression($expr),
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
                $response->headers->set(
                    'Location',
                    $this->generateUrl(
                        'phlexible_api_message_get_message',
                        array('siterootId' => $message->getId()),
                        true
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
