<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * List controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/documenttypes")
 * @Security("is_granted('documenttypes')")
 */
class ListController extends Controller
{
    /**
     * List documenttypes
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="documenttypes_list")
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('phlexible_documenttype.documenttype_manager');

        $allDocumenttypes = $repository->findAll();

        $documenttypes = array();
        foreach ($allDocumenttypes as $documenttype) {
            $documenttypes[] = array(
                'id'        => $documenttype->getKey(),
                'key'       => $documenttype->getKey(),
                'type'      => $documenttype->getType(),
                'de'        => $documenttype->getTitle('de'),
                'en'        => $documenttype->getTitle('en'),
                'mimetypes' => $documenttype->getMimetypes(),
                'icon16'    => $documenttype->hasIcon(16),
                'icon32'    => $documenttype->hasIcon(32),
                'icon48'    => $documenttype->hasIcon(48),
                'icon256'   => $documenttype->hasIcon(256),
            );
        }

        return new JsonResponse(array(
            'totalCount'    => count($documenttypes),
            'documenttypes' => $documenttypes
        ));
    }
}
