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

use Phlexible\Bundle\GuiBundle\Event\PollEvent;
use Phlexible\Bundle\GuiBundle\GuiEvents;
use Phlexible\Bundle\GuiBundle\Poller\MessageCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $lastPoll = $request->getSession()->get('lastPoll');
        if ($lastPoll) {
            $lastPoll = new \DateTime($lastPoll);
        }
        $messages = new MessageCollection($this->getUser()->getId(), $lastPoll);

        $this->get('event_dispatcher')->dispatch(GuiEvents::POLL, new PollEvent($messages));

        $request->getSession()->set('lastPoll', date('Y-m-d H:i:s'));

        $messages = $this->get('serializer')->serialize($messages, 'json');

        return new Response($messages);
    }
}
