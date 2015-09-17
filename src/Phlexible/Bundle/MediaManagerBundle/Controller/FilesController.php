<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Component\Expression\Serializer\ArrayExpressionSerializer;
use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\Model\FolderInterface;
use Phlexible\Component\Volume\VolumeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Expression\Expr;
use Webmozart\Expression\Logic\Conjunction;

/**
 * File controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA')")
 * @Rest\NamePrefix("phlexible_api_mediamanager_")
 */
class FilesController extends FOSRestController
{
    /**
     * List files.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of File",
     *   section="mediamanager",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getFilesAction(Request $request)
    {
        $folderId = $request->get('folderId');
        $start = $request->get('start', 0);
        $limit = $request->get('limit', 25);
        $sort = $request->get('sort', 'name');
        $dir = $request->get('dir', 'ASC');
        $showHidden = $request->get('show_hidden', false);
        $expression = $request->get('expression');

        if (!$folderId) {
            throw new BadRequestHttpException('No folder ID given');
        }

        $data = array();
        $total = 0;

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $volume = $this->getVolumeByFolderId($folderId);

        $folder = $volume->findFolder($folderId);

        if (
            $this->isGranted('ROLE_SUPER_ADMIN') ||
            $this->isGranted('FILE_READ', $folder)
        ) {
            $expr = Expr::equals($folderId, 'folder');

            if (!$showHidden) {
                $expr->andEquals(false, 'hidden');
            }

            if ($expression) {
                $expression = json_decode($expression, true);
                $expressionSerializer = new ArrayExpressionSerializer();
                $expr = new Conjunction(array($expr, $expressionSerializer->deserialize($expression)));
            }

            $files = $volumeManager->findFilesByExpression($expr, array($sort => $dir), $limit, $start);
            $total = $volumeManager->countFilesByExpression($expr);

            $serializer = $this->get('phlexible_media_manager.file_serializer');

            $data = array();
            foreach ($files as $file) {
                $data[] = $serializer->serialize($volume, $file, $request->getLocale());
            }
        }

        return array(
            'files' => $data,
            'count' => $total,
        );
    }

    /**
     * Return file.
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a File",
     *   section="mediamanager",
     *   output="Phlexible\Component\MediaManager\Model\ExtendedFileInterface",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when file was not found"
     *   }
     * )
     */
    public function getFileAction(Request $request, $fileId)
    {
        $fileVersion = $request->get('fileVersion', 1);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $serializer = $this->get('phlexible_media_manager.file_serializer');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId, $fileVersion);

        if (!$file instanceof FileInterface) {
            throw new NotFoundHttpException('File not found');
        }

        $data = $serializer->serialize($file, $request->getLocale());

        return array(
            'file' => $data,
        );
    }

    /**
     * Update file.
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Update a File",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when file was not found"
     *   }
     * )
     */
    public function putFileAction(Request $request, $fileId)
    {
        $name = $request->get('name', false);
        $hide = $request->get('hide', false);
        $show = $request->get('show', false);
        $folderId = $request->get('folderId', false);

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId);

        $file = $volume->findFile($fileId);

        if (!$file instanceof FileInterface) {
            throw new NotFoundHttpException('File not found');
        }

        if ($name) {
            $volume->renameFile($file, $name, $this->getUser()->getUsername());
        }

        if ($hide) {
            $volume->hideFile($file, $this->getUser()->getUsername());
        }

        if ($show) {
            $volume->showFile($file, $this->getUser()->getUsername());
        }

        if ($folderId) {
            $folder = $volume->findFolder($folderId);
            $volume->moveFile($file, $folder, $this->getUser()->getUsername());
        }
    }

    /**
     * Delete File.
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Delete a File",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the file is not found"
     *   }
     * )
     */
    public function deleteFileAction(Request $request, $fileId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        if (!$file instanceof FileInterface) {
            throw new NotFoundHttpException('File not found');
        }

        $volume->deleteFile($file, $this->getUser()->getUsername());
    }

    /**
     * Hide File.
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @deprecated
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Hide a File",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the file is not found"
     *   }
     * )
     */
    public function hideFileAction(Request $request, $fileId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        if (!$file instanceof FileInterface) {
            throw new NotFoundHttpException('File not found');
        }

        $volume->hide($file, $this->getUser()->getUsername());
    }

    /**
     * Show file.
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @deprecated
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Show a File",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the file is not found"
     *   }
     * )
     */
    public function showFileAction(Request $request, $fileId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        if (!$file instanceof FileInterface) {
            throw new NotFoundHttpException('File not found');
        }

        $volume->show($file, $this->getUser()->getUsername());
    }

    /**
     * Rename file.
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @deprecated
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Rename a File",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the file is not found"
     *   }
     * )
     */
    public function renameFileAction(Request $request, $fileId)
    {
        $name = $request->get('name');

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        if (!$file instanceof FileInterface) {
            throw new NotFoundHttpException('File not found');
        }

        $volume->renameFile($file, $name, $this->getUser()->getUsername());
    }

    /**
     * Copy file.
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @deprecated
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Rename a File",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the file is not found"
     *   }
     * )
     */
    public function copyFileAction(Request $request, $fileId)
    {
        $folderId = $request->get('folderId');

        $volume = $this->getVolumeByFolderId($folderId);
        $file = $volume->findFile($fileId);

        if (!$file instanceof FileInterface) {
            throw new NotFoundHttpException('File not found');
        }

        $folder = $volume->findFolder($folderId);

        if (!$folder instanceof FolderInterface) {
            throw new NotFoundHttpException('File not found');
        }

        $volume->copyFile($file, $folder, $this->getUser()->getUsername());
    }

    /**
     * Move file.
     *
     * @param Request $request
     * @param string  $fileId
     *
     * @return Response
     *
     * @deprecated
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Rename a File",
     *   section="mediamanager",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the file is not found"
     *   }
     * )
     */
    public function moveFileAction(Request $request, $fileId)
    {
        $folderId = $request->get('folderId');

        $volume = $this->getVolumeByFolderId($folderId);

        $file = $volume->findFile($fileId);

        if (!$file instanceof FileInterface) {
            throw new NotFoundHttpException('File not found');
        }

        $folder = $volume->findFolder($folderId);

        if (!$folder instanceof FolderInterface) {
            throw new NotFoundHttpException('File not found');
        }

        $volume->moveFile($file, $folder, $this->getUser()->getUsername());
    }

    /**
     * @param string $folderId
     *
     * @return VolumeInterface
     */
    private function getVolumeByFolderId($folderId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        return $volumeManager->getByFolderId($folderId);
    }
}
