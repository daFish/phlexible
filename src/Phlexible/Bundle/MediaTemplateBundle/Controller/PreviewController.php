<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Preview controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatemplates/preview")
 * @Security("is_granted('ROLE_MEDIA_TEMPLATES')")
 */
class PreviewController extends Controller
{
    /**
     * List Image Mediatemplates.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/image", name="mediatemplates_preview_image")
     */
    public function imageAction(Request $request)
    {
        $serializer = $this->get('serializer');
        $previewer = $this->get('phlexible_media_template.previewer');
        $locator = $this->get('file_locator');

        $previewImage = 'test_1000_600.jpg';
        if (isset($params['file'])) {
            $previewImage = $params['file'];
            unset($params['file']);
            if ($previewImage === '800_600') {
                $previewImage = "test_$previewImage.png";
            } else {
                $previewImage = "test_$previewImage.jpg";
            }
        }

        $template = $serializer->deserialize($request->get('params'), 'Phlexible\Component\MediaTemplate\Domain\AbstractTemplate', 'json');

        $filePath = $locator->locate("@PhlexibleMediaTemplateBundle/Resources/public/images/$previewImage", null, true);
        $data = $previewer->create($template, $filePath);

        return new ResultResponse(true, 'Preview created', $data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/audio", name="mediatemplates_preview_audio")
     */
    public function audioAction(Request $request)
    {
        $serializer = $this->get('serializer');
        $previewer = $this->get('phlexible_media_template.previewer');
        $locator = $this->get('file_locator');

        $template = $serializer->deserialize($request->get('params'), 'Phlexible\Component\MediaTemplate\Domain\AbstractTemplate', 'json');

        $filePath = $locator->locate('@PhlexibleMediaTemplateBundle/Resources/public/audio/test.mp3', null, true);
        $data = $previewer->create($template, $filePath);

        return new ResultResponse(true, 'Preview created', $data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/video", name="mediatemplates_preview_video")
     */
    public function videoAction(Request $request)
    {
        $serializer = $this->get('serializer');
        $previewer = $this->get('phlexible_media_template.previewer');
        $locator = $this->get('file_locator');

        $template = $serializer->deserialize($request->get('params'), 'Phlexible\Component\MediaTemplate\Domain\AbstractTemplate', 'json');

        $filePath = $locator->locate('@PhlexibleMediaTemplateBundle/Resources/public/video/test.mpg', null, true);
        $data = $previewer->create($template, $filePath);

        return new ResultResponse(true, 'Preview created', $data);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/get", name="mediatemplates_preview_get")
     */
    public function getAction(Request $request)
    {
        $filename = $request->get('file');
        $filename = $this->container->getParameter('phlexible_media_template.previewer.temp_dir').basename($filename);

        $file = new File($filename);

        return new Response(file_get_contents($filename), 200, array('Content-type' => $file->getMimeType()));
    }
}
