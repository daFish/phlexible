<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Controller;

use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Media controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * Deliver a media asset
     *
     * @param string $fileId
     * @param string $template
     *
     * @return Response
     * @Route("/thumbnail/{fileId}/{template}", name="cms_thumbnail")
     */
    public function thumbnailAction($fileId, $template)
    {
        $templateKey = str_replace('.jpg', '', $template);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $templateManager = $this->get('phlexible_media_template.template_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);
        $template = $templateManager->find($templateKey);

        $filePath = $this->container->getParameter('app.web_dir') . '/media/thumbnail/' . $fileId . '/' . $templateKey . '_' . $template->getRevision() . '.jpg';
        $mimeType = 'image/jpeg';
        if (!file_exists($filePath)) {
            if (file_exists($file->getPhysicalPath())) {
                if (!file_exists(dirname($filePath))) {
                    mkdir(dirname($filePath), 0777, true);
                }

                $spec = $this->get('phlexible_media_cache.specifier')->specify($template);
                $this->get('phlexible_media.transmuter')->transmute($file->getPhysicalPath(), $spec, $filePath);
            } else {
                if (!$template instanceof ImageTemplate) {
                    throw new NotFoundHttpException('Not found');
                }

                $mediaClassifier = $this->get('phlexible_media.media_classifier');
                $delegateService = $this->get('phlexible_media_cache.image_delegate.service');

                $mediaType = $mediaClassifier->getCollection()->get($file->getMediaType());
                $filePath = $delegateService->getClean($template, $mediaType, true);
                $mimeType = 'image/gif';
            }
        }

        if (!file_exists($filePath)) {
            return $this->createNotFoundException("File not found.");
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        return $this->get('igorw_file_serve.response_factory')
            ->create(
                $filePath,
                $mimeType, array(
                    'serve_filename' => $file->getName() . '.' . $extension,
                    'absolute_path' => true,
                )
            );
    }

    /**
     * Download a media file
     *
     * @param string $fileId
     *
     * @return Response
     * @Route("/download/{fileId}", name="cms_download")
     */
    public function downloadAction($fileId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        $filePath = $file->getPhysicalPath();

        if (!file_exists($filePath)) {
            return $this->createNotFoundException("File not found.");
        }

        $mimeType = $file->getMimeType();

        return $this->get('igorw_file_serve.response_factory')
            ->create(
                $filePath,
                $mimeType, array(
                    'serve_filename' => $file->getName(),
                    'absolute_path' => true,
                    'inline' => false,
                )
            );
    }

    /**
     * Deliver a media asset
     *
     * @param string $fileId
     *
     * @return Response
     * @Route("/inline/{fileId}", name="cms_inline")
     */
    public function inlineAction($fileId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        $filePath = $file->getPhysicalPath();

        if (!file_exists($filePath)) {
            return $this->createNotFoundException("File not found.");
        }

        $mimeType = $file->getMimeType();

        return $this->get('igorw_file_serve.response_factory')
            ->create(
                $filePath,
                $mimeType, array(
                    'serve_filename' => $file->getName(),
                    'absolute_path' => true,
                    'inline' => false,
                )
            );
    }

    /**
     * @param string $fileId
     * @param int    $size
     *
     * @return Response
     * @Route("/icon/{fileId}/{size}", name="cms_icon")
     */
    public function iconAction($fileId, $size = 16)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $mediaClassifier = $this->get('phlexible_media.media_classifier');
        $iconResolver = $this->get('phlexible_media_type.icon_resolver');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);
        $mimeType = $file->getMimeType();

        $mediaType = $mediaClassifier->getCollection()->lookup($mimeType);
        $icon = $iconResolver->resolve($mediaType, $size);

        return $this->get('igorw_file_serve.response_factory')
            ->create($icon, 'image/gif', ['absolute_path' => true]);
    }
}
