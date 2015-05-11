<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tree controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/tree")
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 */
class TreeController extends Controller
{
    /**
     * Save an Element Type data tree
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="elementtypes_tree_save")
     */
    public function saveAction(Request $request)
    {
        $treeSaver = $this->get('phlexible_elementtype.tree_saver');

        $elementtype = $treeSaver->save($request, $this->getUser());

        return new ResultResponse(
            true,
            "Element Type {$elementtype->getTitle()} saved as version {$elementtype->getRevision()}."
        );
    }
}
