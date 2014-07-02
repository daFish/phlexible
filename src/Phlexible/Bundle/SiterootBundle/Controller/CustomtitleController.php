<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Custom titles controller
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 * @Route("/siteroots/customtitle")
 * @Security("is_granted('siteroots')")
 */
class CustomtitleController extends Controller
{
    /**
     * Load the form data.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/load", name="siteroots_customtitle_load")
     */
    public function loadAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $language = 'en';

        $siterootRepository = $this->getDoctrine()->getRepository('PhlexibleSiterootBundle:Siteroot');
        $titleResolver = $this->get('phlexible_siteroot.title.resolver');

        $siteroot = $siterootRepository->find($siterootId);

        $headTitle      = $siteroot->getHeadTitle();
        $startHeadTitle = $siteroot->getStartHeadTitle();

        // get all siteroot urls
        $data = array(
            'head_title'       => $headTitle,
            'example'          => $titleResolver->replaceExample($siteroot, $headTitle, $language),
            'start_head_title' => $startHeadTitle,
            'start_example'    => $titleResolver->replaceExample($siteroot, $startHeadTitle, $language),
        );

        return new ResultResponse(true, 'Siteroot customtitles loaded.', $data);
    }

    /**
     * @return JsonResponse
     * @Route("/placeholders", name="siteroots_customtitle_placeholders")
     */
    public function placeholdersAction()
    {
        $language = 'en';

        $titleResolver = $this->get('phlexible_siteroot.title.resolver');

        $data = array(
            'placeholders' => $titleResolver->getPlaceholders($language)
        );

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/example", name="siteroots_customtitle_example")
     */
    public function exampleAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $headTitle = $request->get('head_title');
        $language = 'en';

        $siterootRepository = $this->getDoctrine()->getRepository('PhlexibleSiterootBundle:Siteroot');
        $titleResolver = $this->get('phlexible_siteroot.title.resolver');

        $siteroot = $siterootRepository->find($siterootId);

        $data = array(
            'example' => $titleResolver->replaceExample($siteroot, $headTitle, $language)
        );

        return new ResultResponse(true, 'Siteroot customtitles loaded.', $data);
    }
}