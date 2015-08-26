<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Controller;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Site\Domain\Site;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Online controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OnlineController extends Controller
{
    /**
     * @param Request     $request
     * @param NodeContext $node
     * @param Site    $siteroot
     *
     * @return Response
     * @Route("/", name="cms_online")
     */
    public function indexAction(Request $request, NodeContext $node, Site $siteroot)
    {
        return $this->render($node->getTemplate(), array('node' => $node, 'siteroot' => $siteroot));
    }
}
