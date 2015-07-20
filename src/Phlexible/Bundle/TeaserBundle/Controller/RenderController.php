<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

use Phlexible\Bundle\TeaserBundle\Teaser\TeaserContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Render controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/_teaser/render")
 */
class RenderController extends Controller
{
    /**
     * Render action
     *
     * @param Request $request
     * @param int     $teaserId
     *
     * @return Response
     * @Route("/{_locale}/{teaserId}", name="teaser_render")
     */
    public function htmlAction(Request $request, $teaserId)
    {
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $teaser = $teaserManager->find($teaserId);
        $node = $treeManager->getByNodeId($teaser->getNodeId())->get($teaser->getNodeId());
        $teaserContext = new TeaserContext($teaserManager, $teaser, $node, $request->getLocale());

        $request->attributes->set('contentDocument', $teaserContext);

        $renderConfigurator = $this->get('phlexible_cms.configurator');
        $renderConfig = $renderConfigurator->configure($request);

        if ($request->get('template')) {
            $template = $request->get('template');
        } else {
            $template = $teaserContext->template();
        }

        //$data = $renderConfig->getVariables();

        return $this->render($template, array('teaser' => $teaserContext));
    }
}
