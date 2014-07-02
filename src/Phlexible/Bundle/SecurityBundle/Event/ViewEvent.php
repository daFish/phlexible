<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Event;

use Phlexible\Bundle\SecurityBundle\View\LoginView;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * View event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ViewEvent extends Event
{
    /**
     * @var LoginView
     */
    private $view;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request   $request
     * @param LoginView $view
     */
    public function __construct(Request $request, LoginView $view)
    {
        $this->request = $request;
        $this->view    = $view;
    }

    /**
     * @return LoginView
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}