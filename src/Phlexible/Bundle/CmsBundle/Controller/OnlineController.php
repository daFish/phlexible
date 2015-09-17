<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\CmsBundle\Controller;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Site\Domain\Site;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Online controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OnlineController extends Controller
{
    /**
     * @param Request     $request
     * @param NodeContext $node
     * @param Site        $siteroot
     *
     * @return Response
     * @Route("/", name="cms_online")
     */
    public function indexAction(Request $request, NodeContext $node, Site $siteroot)
    {
        return $this->render($node->getTemplate(), array('node' => $node, 'siteroot' => $siteroot));
    }
}
