<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/data")
 * @Security("is_granted('elementtypes')")
 */
class DataController extends Controller
{
    /**
     * Return available functions
     *
     * @return JsonResponse
     * @Route("/select")
     */
    public function selectAction()
    {
        $callback = $this->get('componentCallback');
        $selectFieldProviders = $callback->getSelectFieldProvider();

        $data = array();
        foreach ($selectFieldProviders as $selectFieldProvider) {
            if (!class_exists($selectFieldProvider)) {
                continue;
            }

            $data[] = array(
                'function' => $selectFieldProvider,
                'title'    => call_user_func(array($selectFieldProvider, 'getTitle')),
            );
        }

        return new JsonResponse(array('functions' => $data));
    }

    /**
     * Return available content channels
     *
     * @return JsonResponse
     * @Route("/contentchannels", name="elementtypes_data_contentchannels")
     */
    public function contentchannelsAction()
    {
        $allContentChannels = $this->get('phlexible_contentchannel.contentchannel_manager')->findAll();

        $contentChannels = array();
        foreach ($allContentChannels as $contentChannelID => $contentChannel) {
            $contentChannels[] = array(
                'id'        => $contentChannelID,
                'title'     => $contentChannel->getTitle(),
                'available' => false
            );
        }

        return new JsonResponse(array('contentChannels' => $contentChannels));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/images", name="elementtypes_data_images")
     */
    public function imagesAction(Request $request)
    {
        $prefix = $request->getBasePath() . '/bundles/elementtypes/elementtypes/';

        $finder = new \Symfony\Component\Finder\Finder();
        $dirs = array(dirname(dirname(__FILE__)) . '/Resources/public/elementtypes');

        foreach ($finder->in($dirs)->name('/^[^_].+\.gif$/') as $file) {
            $filename = $file->getFilename();
            $data[$filename] = array(
                'title' => $filename,
                'url'   => $prefix . $filename
            );
        }

        ksort($data);
        $data = array_values($data);

        return new JsonResponse(array('images' => $data));
    }
}