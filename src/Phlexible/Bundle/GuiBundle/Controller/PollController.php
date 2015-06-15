<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Poll controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/gui/poll")
 */
class PollController extends Controller
{
    /**
     * Poll Action
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="phlexible_gui_poll")
     */
    public function pollAction(Request $request)
    {
        $messages = [];

        $data = [];
        foreach ($this->get('phlexible_dashboard.portlets')->all() as $portlet) {
            $data[$portlet->getId()] = $portlet->getData();
        }

        $message = new \stdClass();
        $message->type = 'dashboard';
        $message->event = 'update';
        $message->userId = $this->getUser()->getId();
        $message->data = $data;
        $message->ts = date('Y-m-d H:i:s');

        $messages[] = (array) $message;

        $request->getSession()->set('lastPoll', date('Y-m-d H:i:s'));

        return new JsonResponse($messages);
    }
}
